@extends('layout.main')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <!-- Basic with Icons -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Daftar {{ $title ?? 'Murid' }}</h5>
                @can('admin')
                <div class="">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openModalCreate()"><i class="bx bx-plus"></i> Tambah Murid</button>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm dataTable">
                    <thead class="table-dark">
                        <tr class="text-nowrap">
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat / Tanggal Lahir</th>
                            @can('admin')
                            <th>aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nisn ?? '-' }}</td>
                            <td>{{ $item->name ?? '-' }}</td>
                            <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                            <td>{{ $item->tempat_lhr ?? '-' }} / {{ $item->tanggal_lhr ?? '--/--/--' }}</td>
                            @can('admin')
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-sm btn-warning text-white btn-icon me-1" onclick="openModalEdit('{{ $item }}')"> 
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form id="deleteForm-{{ $item->id }}" action="{{ route('master/aktor/murid.destroy', encrypt($item->id)) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-icon btn-danger text-white" onclick="confirmDelete('{{ $item->id }}')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endcan
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
            <form action="{{ route('master/aktor/murid.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah {{ $title }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="nisn">NISN *</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="Enter your NISN" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="jenis_kelamin">Jenis Kelamin *</label>
                            <div class="col-sm-10">
                                <select name="jenis_kelamin" class="form-control" id="jenis_kelamin" required>
                                    @foreach ($arrJk as $jk)
                                        @if (old('jenis_kelamin') === $jk)
                                            <option value="{{ $jk }}" selected>{{ $jk }}</option>
                                        @else
                                            <option value="{{ $jk }}">{{ $jk }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="tempat_lhr">Tempat / Tanggal Lahir</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" value="{{ old('tempat_lhr') }}" placeholder="Birth Place" />
                                    <span class="input-group-text" id="basic-addon13">/</span>
                                    <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" value="{{ old('tanggal_lhr') }}" placeholder="Birth Place" />
                                </div>
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
            <form data-action="{{ route('master/aktor/murid.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit {{ $title ?? '-' }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="nisn">NISN *</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="Enter your NISN" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="jenis_kelamin">Jenis Kelamin *</label>
                            <div class="col-sm-10">
                                <select name="jenis_kelamin" class="form-control" id="jenis_kelamin" required>
                                    @foreach ($arrJk as $jk)
                                        @if (old('jenis_kelamin') === $jk)
                                            <option value="{{ $jk }}" selected>{{ $jk }}</option>
                                        @else
                                            <option value="{{ $jk }}">{{ $jk }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="tempat_lhr">Tempat / Tanggal Lahir</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" value="{{ old('tempat_lhr') }}" placeholder="Birth Place" />
                                    <span class="input-group-text" id="basic-addon13">/</span>
                                    <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" value="{{ old('tanggal_lhr') }}" placeholder="Birth Place" />
                                </div>
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
                form.querySelector('#nisn').value = item.nisn;
                form.querySelector('#name').value = item.name;
                form.querySelector('#jenis_kelamin').value = item.jenis_kelamin;
                form.querySelector('#tempat_lhr').value = item.tempat_lhr;
                form.querySelector('#tanggal_lhr').value = item.tanggal_lhr;
                
                var modalEdit = new bootstrap.Modal(editModal);
                modalEdit.show();
            }
        </script>
    @endpush
@endsection