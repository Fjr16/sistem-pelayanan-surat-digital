@extends('layout.main')

@push('page-css')
<style>
   .surat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .surat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .surat-card.disabled-card {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none; /* blok klik */
        box-shadow: none !important; /* hilangkan efek hover */
    }
    .icon-wrapper {
      width: clamp(45px, 5vw, 60px); ;
      height: clamp(45px, 5vw, 60px);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      color: #fff;
      font-size: clamp(1.2rem, 2vw, 1.8rem);
      margin-right: 15px;
      flex-shrink: 0;
    }
    .theme-1 { background: #4e73df; }
    .theme-2 { background: #1cc88a; }
    .theme-3 { background: #e74a3b; }
    .theme-4 { background: #f6c23e; }
    .theme-5 { background: #36b9cc; }
    .theme-6 { background: #6f42c1; }
</style>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h3 class="mb-4 text-center">Daftar Jenis Surat</h3>
    <div class="row g-4">
        @foreach ($data as $item)    
        @php
            $icons = [
                'bx bx-home',
                'bx bx-envelope',
                'bx bx-user',
                'bx bx-file',
                'bx bx-archive',
                'bx bx-folder',
                'bx bx-buildings',
                'bx bx-cube',
                'bx bx-world',
                'bx bx-book',
                'bx bx-printer'
            ];
            $randomIcon = $icons[array_rand($icons)];
        @endphp
            <div class="col-sm-6 col-lg-4">
                <div class="card surat-card {{ $item->is_active ? '' : 'disabled-card' }} h-100 border-0 shadow-sm" data-id="1">
                    <div class="card-body d-flex align-items-start">
                        <div class="icon-wrapper theme-{{ rand(1,6) }}">
                            <i class="{{ $randomIcon }}"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">{{ $item->name ?? '-' }}</h5>
                            <p class="card-text text-muted small mb-2">
                                {{ Str::limit($item->description, 100, '...') }}
                            </p>
                            <span class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">{{ $item->is_active ? 'Tersedia' : 'Tidak Tersedia' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    // Step 1: pilih jenis surat
    $('#jenis_surat').on('change', function(){
        if ($(this).val()) {
            $('#btn-next').prop('disabled', false);
        } else {
            $('#btn-next').prop('disabled', true);
        }
    });

    // Next ke Step 2
    $('#btn-next').on('click', function(){
        let suratId = $('#jenis_surat').val();
        $('#jenis_surat_id').val(suratId);

        // Load form persyaratan via ajax
        $.get("{{ url('jenis-surat/schema') }}/" + suratId, function(res){
            if (res.schema) {
                var renderWrap = $('#form-persyaratan');
                renderWrap.empty();

                // render formBuilder data → formRender
                var formRender = $('<div/>');
                formRender.formRender({
                    dataType: 'json',
                    formData: res.schema
                });
                renderWrap.append(formRender);
            }
        });

        // ganti tab
        $('#step1-tab').removeClass('active');
        $('#step1').removeClass('show active');
        $('#step2-tab').removeClass('disabled').addClass('active');
        $('#step2').addClass('show active');
    });

    // Back ke Step 1
    $('#btn-back').on('click', function(){
        $('#step2-tab').removeClass('active');
        $('#step2').removeClass('show active');
        $('#step1-tab').addClass('active');
        $('#step1').addClass('show active');
    });
});
</script>
@endpush
