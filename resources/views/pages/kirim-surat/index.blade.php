@extends('layout.main')

@push('page-css')
<style>
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 6px 6px 0 0;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    .nav-tabs .nav-link:hover {
        background-color: rgba(0,0,0,0.05);
    }

    .nav-tabs .nav-link.active {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <div class="card">
            <div class="card-header">
                <h5>{{ $title ?? '-' }}</h5>
            </div>
            <div class="card-body">

                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs nav-fill" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::finish->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-hourglass me-2"></i>
                            <span class="fw-semibold">Validasi</span>
                            <span class="badge bg-warning text-dark rounded-pill ms-2 text-white">{{ $countSurat->finishCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::sent->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-check-circle me-2"></i>
                            <span class="fw-semibold">Selesai</span>
                            <span class="badge bg-success text-dark rounded-pill ms-2 text-white">{{ $countSurat->sentCount }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3 px-0">
                    <div class="tab-pane fade show active" id="content" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-sm" id="tabel-data">
                                <thead class="table-dark">
                                    <tr class="text-nowrap">
                                        <th>Action</th>
                                        <th>Nama Surat</th>
                                        <th>Diajukan Pada</th>
                                        <th>Diajukan Penduduk</th>
                                        <th>Dibantu Petugas</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page-js')
    <script>
        var table;
        var status;

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                    url:"{{ route('proses/surat/pengesahan.show') }}",
                    data:function(d){
                        d.status = status;
                    }
                },
                columns:[
                    {
                        data:'action',
                        name:'action',
                        orderable:false,
                        searchable:false
                    },
                    {
                        data:'mail_name',
                        name:'mails.name'
                    },
                    {
                        data:'incoming_mails.created_at',
                        name:'incoming_mails.created_at'
                    },
                    {
                        data:'penduduk_name',
                        name:'pend.name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'petugas_name',
                        name:'pet.name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'status',
                        name:'status',
                        orderable:false,
                        'defaultContent' : '-'
                    },
                ],
                order:[
                    [2, 'desc']
                ]
            });

            $('#roleTabs button').on('click', function(){
                var tabId = $(this).attr('id');
                status = tabId.replace('tab-', '');
                table.ajax.reload();
            });
        });


        async function signAndSend(incomingMailId) {
            // Optional: tampilkan loading
            Swal.fire({
                title: 'Menyiapkan preview PDF...',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false
            });

            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('incoming_mail_id', incomingMailId);

            try {
                const response = await fetch("{{ route('proses/surat.insertQrToPdf') }}", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) {
                    const errText = await response.text(); // ambil isi response
                    const resErr = JSON.parse(errText);
                    throw new Error(resErr.message);
                }

                // Ambil blob PDF dari response
                const pdfBlob = await response.blob();
                const pdfUrl = URL.createObjectURL(pdfBlob);

                Swal.close(); // tutup loading

                // Tampilkan preview PDF di modal atau tab baru
                const previewHtml = `
                    <iframe src="${pdfUrl}" width="100%" height="600px" style="border:none;"></iframe>
                `;

                Swal.fire({
                    title: 'Preview PDF',
                    html: previewHtml,
                    width: 900,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan Final',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveFinalPdf(incomingMailId, pdfBlob);
                    }
                });

            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message
                });
            }
        }

        async function saveFinalPdf(incomingMailId, pdfBlob) {
            const formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('incoming_mail_id', incomingMailId);
            formData.append('surat_pdf', pdfBlob, 'final_surat.pdf'); // nama file bisa dinamis

            try {
                const response = await fetch("{{ route('proses/surat/pengesahan.store') }}", {
                    method: "POST",
                    body: formData
                });

                const resJson = await response.json();
                if(resJson.status){
                    Toast.fire({
                        icon:'success',
                        text: resJson.message
                    });
                } else {
                    Toast.fire({
                        icon:'error',
                        text: resJson.message
                    });
                }
                setTimeout(() => {
                    window.location.reload();
                }, 2000);

            } catch(err){
                console.error(err);
                Swal.fire('Error', err.message, 'error');
            }
        }

        function showLetter(pdfUrl){
            const previewHtml = `
                <iframe src="${'/storage/' + pdfUrl}" width="100%" height="600px" style="border:none;"></iframe>
            `;

            Swal.fire({
                title: 'Preview PDF',
                html: previewHtml,
                width: 900,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Kembali',
                cancelButtonColor: '#d33'
            });
        }


    </script>
    @endpush
@endsection
