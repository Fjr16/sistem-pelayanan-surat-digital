@extends('layout.main')

@push('page-css')
<style>
    /* .nav-tabs .nav-link {
        border-radius: 4px;
        font-weight: 500 !important;
        margin-right: 5px;
        padding: 6px 15px;
        transition: 0.2s;
    } */
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
        /* border-radius: 8px 8px 0 0; */
    }

    /* .nav-tabs .nav-link.active {
        border-bottom: 3px solid currentColor;
        background-color: rgba(0,0,0,0.03);
    } */

    .nav-tabs .nav-link.active {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ $title ?? 'Pengguna' }}</h5>
                <div class="">
                    <a href="{{ route('pengajuan/surat.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-mail-send me-2" style="font-size:20px;"></i> Ajukan Sekarang</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs nav-fill" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-pending"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-hourglass me-2"></i>
                            <span class="fw-semibold">Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill ms-2 text-white">4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-process"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-loader-circle me-2"></i>
                            <span class="fw-semibold">Dalam Pengerjaan</span>
                            <span class="badge bg-info text-dark rounded-pill ms-2 text-white">4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-rejected"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-block me-2"></i>
                            <span class="fw-semibold">Ditolak</span>
                            <span class="badge bg-danger text-dark rounded-pill ms-2 text-white">4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-process"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-minus-circle me-2"></i>
                            <span class="fw-semibold">Dibatalkan</span>
                            <span class="badge bg-secondary text-dark rounded-pill ms-2 text-white">4</span>
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
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No. HP / WA</th>
                                        <th>NIK</th>
                                        <th>Tempat Tgl Lahir</th>
                                        <th>Status Kawin</th>
                                        <th>Role</th>
                                        <th>Aktif</th>
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
                    url:"{{ route('user.getData') }}",
                    data: function(d){
                        d.role_id = status;
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
                        data:'name',
                        name:'name'
                    },
                    {
                        data:'username',
                        name:'username'
                    },
                    {
                        data:'email',
                        name:'email'
                    },
                    {
                        data:'gender',
                        name:'gender'
                    },
                    {
                        data:'no_wa',
                        name:'no_wa'
                    },
                    {
                        data:'nik',
                        name:'nik'
                    },
                    {
                        data:'ttl',
                        name:'ttl'
                    },
                    {
                        data:'status_kawin',
                        name:'status_kawin'
                    },
                    {
                        data:'role.name',
                        name:'role.name'
                    },
                    {
                        data:'is_active',
                        name:'is_active'
                    }
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
    </script>
    @endpush
@endsection
