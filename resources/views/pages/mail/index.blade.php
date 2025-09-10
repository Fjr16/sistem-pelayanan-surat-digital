@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="m-0 p-0">{{ $title ?? 'Data Jenis Surat' }}</h5>
            <a href="{{ route('jenis/surat.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> Buat Surat</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr class="table-dark m-0 p-0">
                            <th>No</th>
                            <th>Nama Surat</th>
                            <th>Deskripsi</th>
                            <th>Persyaratan</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr class="text-wrap">
                            <td scope="row">{{ $loop->iteration }}</td>
                            <td>{{ $item->name ?? '-' }}</td>
                            <td>{{ $item->description ? Str::limit($item->description, 200, ' ...') : '-' }}</td>
                            <td class="text-nowrap">
                                <ol>
                                    @foreach ($item->mailRequirements as $detail)
                                    <li>{{ $detail->field_label ?? '-' }}</li>
                                    @endforeach
                                </ol>
                            </td>
                            <td><span class="badge bg-{{ $item->is_active ? 'primary' : 'danger' }}"><i class="bx bx-{{ $item->is_active ? 'check' : 'x' }}-circle" style="font-size: 18px"></i></span> </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('jenis/surat.create', ['edit' => encrypt($item->id)]) }}" class="btn btn-sm btn-warning text-white btn-icon me-1">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form id="deleteForm-{{ $item->id }}" action="{{ route('jenis/surat.destroy', encrypt($item->id)) }}" method="POST">
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
@endpush
@endsection
