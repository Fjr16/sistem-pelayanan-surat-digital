@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <!-- Basic Layout -->
    @can('admin')
    <div class="card mb-4">
        <h5 class="card-header">Tambah {{ $title ?? 'Hari' }}</h5>
        <div class="card-body">
            <form action="{{ route('master/hari.store') }}" method="POST">
                @csrf
                <div class="row mb-6">
                    <div class="col-sm-2">
                        <label class="form-label" for="order_number">Urutan</label>
                        <input type="number" class="form-control" name="order_number" id="order_number" min="1" value="1" required>
                    </div>
                    <div class="col-sm-10">
                        <label class="form-label" for="name">Nama Hari</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Senin" required/>
                    </div>
                </div>

                <h5 class="border-top">Periode Pelajaran</h5>
                <div id="lesson_period_container">
                    <div class="row mb-2" id="lesson_period_1">
                        <div class="col-sm-2">
                            <label class="form-label" for="period_number_1">Periode Ke</label>
                            <input type="number" class="form-control" name="period_number[]" id="period_number_1" min="1" value="1">
                        </div>
                        <div class="col-sm-10">
                            <label class="form-label" for="start">Rentang Waktu</label>
                            <div class="d-flex align-items-center">
                                <div class="input-group">
                                    <input type="time" class="form-control" name="start[]" id="start_1" value=""/>
                                    <span class="input-group-text" id="basic-addon13">-</span>
                                    <input type="time" class="form-control" name="end[]" id="end_1" value=""/>
                                </div>
                                <button type="button" class="btn btn-icon btn-sm btn-primary ms-1" onclick="addInput()"><i class="bx bx-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    </div>
    @endcan

    <div class="card">
        <h5 class="card-header">Daftar {{ $title ?? '' }}</h5>
        <div class="card-body">

            <div id="accordionIcon" class="accordion accordion-without-arrow">
                @foreach ($data as $item)
                    <div class="accordion-item">
                        <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionIconOne">
                            <div class="accordion-button-wrapper w-100">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <button
                                            type="button"
                                            class="accordion-button collapsed"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#accordionIcon-{{ $loop->iteration }}"
                                            aria-controls="accordionIcon-{{ $loop->iteration }}">
                                            [{{ $item->order_number ?? '-' }}] {{ $item->name ?? '-' }}
                                        </button>
                                    </div>
                                    @can('admin')
                                    <div class="col-sm-2 text-end">
                                        <a class="btn btn-sm btn-warning text-white btn-icon me-1"
                                           href="{{ route('master/hari.edit', encrypt($item->id)) }}"> 
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form id="deleteForm-{{ $item->id }}" action="{{ route('master/hari.destroy', encrypt($item->id)) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-icon btn-danger text-white" onclick="confirmDelete('{{ $item->id }}')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        </h2>
                        
                
                    <div id="accordionIcon-{{ $loop->iteration }}" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                        <div class="accordion-body">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-sm">
                                <thead class="table-dark">
                                    <tr class="text-nowrap">
                                        <th>Periode Ke</th>
                                        <th>Start</th>
                                        <th>end</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->lessonPeriods as $detail)
                                    <tr>
                                        <td>{{ $detail->period_number ?? '-' }}</td>
                                        <td>{{ $detail->start ?? '-' }}</td>
                                        <td>{{ $detail->end ?? '-' }}</td>
                                        {{-- <td>
                                            
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                @endforeach
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

        {{-- addInput --}}
        <script>
            let count = 2;
            function addInput(){
                let newRow = `
                    <div class="row mb-2" id="lesson_period_${count}">
                        <div class="col-sm-2">
                            <input type="number" class="form-control" name="period_number[]" id="period_number_${count}" min="1" value="${count}">
                        </div>
                        <div class="col-sm-10">
                            <div class="d-flex align-items-center">
                                <div class="input-group">
                                    <input type="time" class="form-control" name="start[]" id="start_${count}" value=""/>
                                    <span class="input-group-text" id="basic-addon13">-</span>
                                    <input type="time" class="form-control" name="end[]" id="end_${count}" value=""/>
                                </div>
                                <button type="button" class="btn btn-icon btn-sm btn-danger ms-1" onclick="removeInput(this)"><i class="bx bx-minus"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                count++
                $('#lesson_period_container').append(newRow);
            }
            function removeInput(element) {
                $(element).closest('.row').remove();
            }
        </script>
    @endpush
@endsection