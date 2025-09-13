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

    @push('page-js')
    <script>
        var table;

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:"{{ route('proses/surat/upload.show') }}",
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
        });


        async function uploadSurat(incomingMailId){
            const { value: file } = await Swal.fire({
                title: "Upload Surat",
                input: "file",
                inputAttributes: {
                    "accept": "application/pdf",
                    "aria-label": "Upload your PDF file"
                },
                showCancelButton:true,
                cancelButtonText:"Kembali",
                cancelButtonColor: "#d33",
                confirmButtonColor: "#3085d6",
                inputValidator: (value) => {
                    if (!value) {
                        return "File tidak boleh kosong!";
                    }
                    if (value && value.type !== "application/pdf") {
                        return "Hanya file PDF yang diperbolehkan!";
                    }
                }
            });

            if (file) {
                const fileURL = URL.createObjectURL(file);
                Swal.fire({
                    title: "Preview PDF",
                    html: `<iframe src="${fileURL}" width="100%" height="600px" style="border:none;"></iframe>`,
                    width: 900,
                    heightAuto: false,
                    showCancelButton:true,
                    cancelButtonText:"Batal",
                    showConfirmButton:true,
                    confirmButtonText:"Ya, Lanjutkan",
                    cancelButtonColor: "#d33",
                    confirmButtonColor: "#3085d6",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url:"{{ route('proses/surat/upload.store') }}",
                            type:"post",
                            data:,
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
                        })
                    }
                });
            }
        }
    </script>
    @endpush
@endsection
