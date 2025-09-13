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
            <div class="card-header d-flex justify-content-between">
                <h5>{{ $title ?? '-' }}</h5>
                <div class="">
                    <a href="{{ route('pengajuan/surat.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-mail-send me-2" style="font-size:20px;"></i> Ajukan Sekarang</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs nav-fill" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::pending->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-hourglass me-2"></i>
                            <span class="fw-semibold">Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill ms-2 text-white">{{ $countSurat->pendingCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::rejected->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-block me-2"></i>
                            <span class="fw-semibold">Ditolak</span>
                            <span class="badge bg-danger text-dark rounded-pill ms-2 text-white">{{ $countSurat->rejectedCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::cancel->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-minus-circle me-2"></i>
                            <span class="fw-semibold">Dibatalkan</span>
                            <span class="badge bg-secondary text-dark rounded-pill ms-2 text-white">{{ $countSurat->cancelCount }}</span>
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
                                        <th>Ket</th>
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
        var status = "";

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                    url:"{{ route('proses/surat/verifikasi.show') }}",
                    data: function(d){
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
                        name:'mail_name'
                    },
                    {
                        data:'incoming_mails.created_at',
                        name:'incoming_mails.created_at'
                    },
                    {
                        data:'penduduk_name',
                        name:'penduduk_name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'petugas_name',
                        name:'petugas_name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'keterangan',
                        name:'keterangan',
                        'defaultContent' : '-'
                    },
                ],
                order:[
                    [2, 'desc']
                ]
            });

            $('#roleTabs button').on('click', function(){
                var tabId = $(this).attr('id');
                if (tabId === 'tab-all') {
                    status = "PENDING";
                }else{
                    status = tabId.replace('tab-', '');
                }
                table.ajax.reload();
            });
        });


        function verifikasi(incomingMailId){
            Swal.fire({
                input: "textarea",
                inputLabel: "Kenapa Anda Membatalkan Pengajuan ?",
                inputPlaceholder: "Type your reason here...",
                inputAttributes: {
                    "aria-label": "Type your message here"
                },
                showCancelButton: true,
                cancelButtonText: "Kembali",
                cancelButtonColor: "#d33",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Lanjutkan !"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:"{{ route('surat/saya/update.status') }}",
                        type:'POST',
                        data:{
                            _token:"{{ csrf_token() }}",
                            incoming_mail_id:incomingMailId,
                            status_update:stts,
                            keterangan:result.value
                        },
                        success:function(res){
                            if (res.status) {
                                Toast.fire({
                                    icon:'success',
                                    text:res.message,
                                });
                            }else{
                                Toast.fire({
                                    icon:'error',
                                    text:res.message,
                                });
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        },error:function(xhr){
                            Toast.fire({
                                icon:'error',
                                text:xhr.responseText().slice(0, 150),
                            });
                        }
                    });
                }
            });
        }
    </script>
    @endpush
@endsection
