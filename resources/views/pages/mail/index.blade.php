@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
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
            <button type="button" id="save_btn" class="btn btn-md btn-primary mt-4">Simpan Form</button>
        </div>
    </div>
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
            var formBuilder = $('#fb-editor').formBuilder({
                controlOrder:['text', 'number', 'textarea', 'date', 'file', 'select', 'radio-group', 'checkbox-group'],
                disableFields:['header', 'autocomplete', 'button', 'hidden', 'paragraph','starRating'],
                typeUserDisabledAttrs:{
                    'text' : [
                        'description',
                        'access',
                    ],
                    'number' : [
                        'description',
                        'access',
                    ],
                    'date' : [
                        'description',
                        'access',
                        'step',
                        'placeholder'
                    ],
                    'textarea' : [
                        'description',
                        'access',
                        'subtype',
                        'rows'
                    ],
                    'file' : [
                        'description',
                        'access',
                        'placeholder',
                        'multiple'
                    ],
                    'select' : [
                        'description',
                        'access',
                        'placeholder',
                        'multiple'
                    ],
                    'radio-group' : [
                        'description',
                        'access',
                        'other',
                        'class'
                    ],
                    'checkbox-group' : [
                        'description',
                        'access',
                        'other',
                        'class',
                        'toggle',
                        // 'value'
                    ]
                }
            });

            $('#save_btn').on('click', function(e) {
                var dataJson = formBuilder.actions.getData('json');
                $.ajax({
                    url: "{{ route('jenis/surat.store') }}",
                    type: "post",
                    data: {
                        _token : "{{ csrf_token() }}",
                        schema : dataJson,
                        name: $('#name').val(),
                        description:$('#description').val()
                    },
                    success:function(res){
                        if (res.status == true) {
                            Toast.fire({
                                icon:"success",
                                title:res.message
                            });
                        }else{
                            Toast.fire({
                                icon:"error",
                                title:res.message
                            });
                        }
                    },
                    error:function(xhr){
                        Toast.fire({
                            icon:"error",
                            title:"gagal Simpan Data"
                        });
                    }
                });
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
    @endpush
@endsection