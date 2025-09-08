@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <!-- Basic Layout -->
    <form action="{{ route('jenis/surat.store') }}" method="POST" id="save-form">
    @csrf
        <div class="card mb-4">
            <h5 class="card-header">Tambah {{ $title ?? 'Jenis Surat' }}</h5>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    {{-- Jenis Surat --}}
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="name">Jenis Surat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name"
                            placeholder="Surat Keterangan .." required>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12 col-md-7">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="1"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Persyaratan Surat</h5>
                <p class="small m-0 p-0 fst-italic">Tambahkan form yang harus diisi ketika surat diajukan, sesuai dengan persyaratan yang dibutuhkan</p>
            </div>
            <div class="card-body">
                <div id="fb-editor"></div>
                <textarea name="schema" id="schema" hidden></textarea>
                <button type="submit" class="btn btn-md btn-primary mt-4">Simpan Form</button>
            </div>
        </div>
    </form>


    {{-- <div class="card">
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
    </div> --}}
  </div>

    <x-confirm-modal/>
    

    @push('page-js')
    {{-- cdn form builder --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>

    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>

    <script>
        jQuery(function($) {
            var formBuilder = $(document.getElementById('fb-editor')).formBuilder();

            $('#save-form').on('submit', function() {
                var dataJson = JSON.stringify(formBuilder.actions.getData('json'));
                console.log(dataJson)
                $('#schema').val(JSON.stringify(formBuilder.actions.getData('json')));
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