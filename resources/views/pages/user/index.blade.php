@extends('layout.main')

@push('page-css')
<style>
    .nav-tabs .nav-link {
        border-radius: 4px;
        font-weight: 500 !important;
        margin-right: 5px;
        padding: 6px 15px;
        transition: 0.2s;
    }
</style>
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Daftar {{ $title ?? 'Pengguna' }}</h5>
                <div class="">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openModalCreate()"><i class="bx bx-plus"></i> Tambah {{ $title ?? 'Pengguna' }}</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                            id="tab-all"
                            data-bs-toggle="tab"
                            data-bs-target="#content-all"
                            type="button"
                            role="tab">
                            <i class="bx bx-group"></i> All Users
                            <span class="badge bg-secondary ms-1">All</span>
                        </button>
                    </li>
                    @foreach($roles as $role)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-{{ $role->id }}"
                                data-bs-toggle="tab"
                                data-bs-target="#content-all"
                                type="button"
                                role="tab">
                                <i class="bx bx-user"></i>
                                {{ $role->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-3 px-0">
                    <div class="tab-pane fade show active" id="content-all" role="tabpanel">
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

    <x-confirm-modal/>

  <!-- Modal create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form id="formAuthenticationCreate" action="{{ route('master/aktor/pengguna.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah {{ $title }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="username">Username *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="email">Email *</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" />
                            </div>
                        </div>
                        <div class="row mb-6 form-password-toogle">
                            <label class="col-sm-2 col-form-label" for="password">Password *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"
                                     />
                                    <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="role_id">Role *</label>
                            <div class="col-sm-10">
                                <select name="role_id" class="form-control" id="role_id" required>
                                    @foreach ($roles as $role)
                                        @if (old('role_id') === $role->id)
                                            <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

  <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <form id="formAuthenticationEdit" data-action="{{ route('master/aktor/pengguna.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit {{ $title ?? '-' }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="username">Username *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="email">Email *</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" />
                            </div>
                        </div>
                        <div class="row mb-6 form-password-toogle">
                            <label class="col-sm-2 col-form-label" for="password">Password *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"
                                     />
                                    <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                                <div class="text-danger small fst-italic">Kosongkan jika tidak ingin mengubah password</div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="role_id">Role *</label>
                            <div class="col-sm-10">
                                <select name="role_id" class="form-control" id="role_id" required>
                                    @foreach ($roles as $role)
                                        @if (old('role_id') === $role->id)
                                            <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('page-js')
    <script>
        var table;
        var selectedRoleId = "";

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                    url:"{{ route('user.getData') }}",
                    data: function(d){
                        d.role_id = selectedRoleId;
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
                    selectedRoleId = "";
                }else{
                    selectedRoleId = tabId.replace('tab-', '');
                }
                table.ajax.reload();
            });
        });
    </script>

    {{-- konfirmasi modal delete --}}
        <script>
            var formToSubmit;
            function confirmDelete(id){
                formToSubmit = document.getElementById('deleteForm-' + id);
                var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                confirmModal.show();
            }

            document.addEventListener('DOMContentLoaded', function(){
                var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                if (confirmDeleteBtn) {
                    confirmDeleteBtn.addEventListener('click', function(){
                        if (formToSubmit) {
                            formToSubmit.submit();
                        }
                    });
                }
            });
        </script>

        {{-- open modal edit --}}
        <script>
            function openModalCreate(){
                const modal = new bootstrap.Modal(document.getElementById('createModal'));
                modal.show();
            }
            function openModalEdit(idUser){
                var editModal = document.getElementById('editModal');
                var form = editModal.querySelector('form');
                var actionTemplate = form.getAttribute('data-action');

                $.ajax({
                    url:"{{ route('user.getDetail') }}",
                    type:'post',
                    data:{
                        _token:"{{ csrf_token() }}",
                        user_id: idUser
                    },
                    success:function(res){
                        if (res.status) {
                            var item = res.data;

                            form.action = actionTemplate.replace(':id', item.id);
                            form.querySelector('#name').value = item.name;
                            form.querySelector('#username').value = item.username;
                            form.querySelector('#email').value = item.email;
                            form.querySelector('#role_id').value = item.role_id;

                            var modalEdit = new bootstrap.Modal(editModal);
                            modalEdit.show();
                        }else{
                            console.log(res.message);
                        }
                    },
                    error:function(xhr){
                        console.log(xhr.responseText)
                    }
                });
            }

        </script>
    @endpush
@endsection
