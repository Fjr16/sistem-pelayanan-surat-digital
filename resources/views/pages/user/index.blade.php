@extends('layout.main')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <!-- Basic with Icons -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Daftar {{ $title ?? 'Pengguna' }}</h5>
                <div class="">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openModalCreate()"><i class="bx bx-plus"></i> Tambah {{ $title ?? 'Pengguna' }}</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm dataTable">
                    <thead class="table-dark">
                        <tr class="text-nowrap">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name ?? '-' }}</td>
                            <td>{{ $item->username ?? '-' }}</td>
                            <td>{{ $item->email ?? '-' }}</td>
                            <td>{{ $item->role ?? '-' }}</td>
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-sm btn-warning text-white btn-icon me-1" onclick="openModalEdit('{{ $item }}')"> 
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form id="deleteForm-{{ $item->id }}" action="{{ route('master/aktor/pengguna.destroy', encrypt($item->id)) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-icon btn-danger text-white" onclick="confirmDelete('{{ $item->id }}')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
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
                            <label class="col-sm-2 col-form-label" for="role">Role *</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control" id="role" required>
                                    @foreach ($arrRole as $role)
                                        @if (old('role') === $role)
                                            <option value="{{ $role }}" selected>{{ $role }}</option>
                                        @else
                                            <option value="{{ $role }}">{{ $role }}</option>
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
                            <label class="col-sm-2 col-form-label" for="role">Role *</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control" id="role" required>
                                    @foreach ($arrRole as $role)
                                        @if (old('role') === $role)
                                            <option value="{{ $role }}" selected>{{ $role }}</option>
                                        @else
                                            <option value="{{ $role }}">{{ $role }}</option>
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
            function openModalEdit(item){
                var item = JSON.parse(item);
                var editModal = document.getElementById('editModal');
                var form = editModal.querySelector('form');
                var actionTemplate = form.getAttribute('data-action');
                form.action = actionTemplate.replace(':id', item.id);
                form.querySelector('#name').value = item.name;
                form.querySelector('#username').value = item.username;
                form.querySelector('#email').value = item.email;
                form.querySelector('#role').value = item.role;
                
                var modalEdit = new bootstrap.Modal(editModal);
                modalEdit.show();
            }
        </script>
    @endpush
@endsection