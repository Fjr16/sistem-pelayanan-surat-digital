@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="card mb-4">
        <h5 class="card-header">{{ $title ?? 'Tambah Jenis Surat' }}</h5>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12">
                    <input type="hidden" name="mail_id" id="mail_id" value="{{ $item?->id }}">
                    <label class="form-label" for="name">Jenis Surat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $item?->name ?? '' }}"
                        placeholder="Surat Keterangan .." required>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Deskripsikan jenis surat disini">{{ $item?->description ?? '' }}</textarea>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $item?->is_active == true ? 'checked' : '' }}/>
                        <label class="form-check-label" for="is_active">Geser ke kiri untuk mengaktifkan jenis surat ini</label>
                    </div>
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
            <button type="button" id="save_btn" class="btn btn-md btn-primary mt-4"><i class="bx bx-file"></i> Simpan</button>
        </div>
    </div>
</div>

@push('page-js')
    {{-- cdn form builder --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>

    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>

    <script>
        var exFormData = @json($formBuilderData);
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
                        'toggle'
                        // 'value'
                    ]
                },
                disabledSubtypes: {
                    number:['range'],
                },
                disabledActionButtons: ['data', 'clear', 'save'],
                formData:exFormData
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
                        description:$('#description').val(),
                        is_active:$('#is_active').prop('checked') ? 1 : 0,
                        mail_id:$('#mail_id').val() ?? null,
                    },
                    success:function(res){
                        const mailId = $('#mail_id').val();
                        const name = $('#name').val();
                        const description = $('#description').val();
                        const isActive = $('#is_active').prop('checked');
                        if (res.status == true) {
                            if (!mailId) {
                                $('#mail_id').val(null);
                                $('#name').val('');
                                $('#description').val('');
                                $('#is_active').prop('checked', false);
                                formBuilder.actions.clearFields();
                            }
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
                            title:xhr.responseText()
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endsection
