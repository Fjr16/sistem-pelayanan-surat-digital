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

    .card-overlay {
        position: absolute;
        inset: 0; /* full cover */
        background: rgba(0, 0, 0, 0.5); /* lapisan gelap transparan */
        border-radius: inherit;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 5;
    }

    .surat-card:hover .card-overlay {
        opacity: 1;
    }
    .swal2-container {
        z-index: 20000 !important; /* lebih besar dari modal fullscreen */
    }
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
                <div class="card surat-card {{ $item->is_active ? '' : 'disabled-card' }} h-100 border-0 shadow-sm" data-id="{{ $item->id }}">
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

                     {{-- Overlay transparan dengan tombol --}}
                    <div class="card-overlay d-flex justify-content-center align-items-center gap-2">
                        <button class="btn btn-sm btn-light" onclick="showDetail({{ $item->id }}, '{{ $item->name }}', `{{ $item->description }}`)">
                            <i class="bx bx-info-circle"></i> Detail
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="modalPengajuan({{ $item->id }})">
                            <i class="bx bx-send"></i> Ajukan
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- modal detail --}}
<div class="modal fade" id="modal-show" tabindex="-1" aria-labelledby="modalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-show-name"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body" id="modal-show-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-submit-show"> <i class="bx bx-send"></i> Ajukan Sekarang</button>
      </div>
    </div>
  </div>
</div>

{{-- modal pengajuan --}}
<div class="modal fade" id="modal-pengajuan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal-pengajuan-title"></h5>
        </div>
        <div class="modal-body">
            <form method="POST" id="modal-pengajuan-form">
                @csrf
                <input type="hidden" id="modal-pengajuan-mail_id" name="mail_id">
                <div id="modal-pengajuan-body"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Kembali</button>
            <button type="button" class="btn btn-primary" onclick="submitPengajuan()">Simpan Pengajuan</button>
        </div>
    </div>
  </div>
</div>
@endsection

@push('page-js')
 {{-- cdn form render --}}
<script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
<script>
    function showDetail(mailId, mailName, mailDesc){
        if (!mailId) {
            Toast.fire({
                icon: "error",
                text: "Surat Tidak Ditemukan",
            });
            return;
        }

        $('#modal-show-name').text(mailName);
        $('#modal-show-body').html(mailDesc);
        $('#btn-submit-show').off('click').on('click', function(){
            $('#modal-show').modal('hide');
            modalPengajuan(mailId);
        });
        $('#modal-show').modal('show');
    }

    function modalPengajuan(mailId){
        if (!mailId) {
            Toast.fire({
                icon: "error",
                text: "Surat Tidak Ditemukan",
            });
            return;
        }

        $.get("{{ url('pengajuan/surat/get/schema') }}/" + mailId, function(res){
            if (res.status) {
                if (!res.data) {
                    Toast.fire({
                        icon: "error",
                        text: "Terjadi Kesalahan, Data surat tidak ditemukan",
                    });
                }else{
                    const mail = res.data.mail;
                    const schema = res.data.mailRequirements;
                    if (schema) {
                        var renderWrap = $('#modal-pengajuan-body');
                        renderWrap.empty();

                        var formRender = $('<div/>');
                        formRender.formRender({
                            dataType: 'json',
                            formData: schema
                        });
                        renderWrap.append(formRender);
                    }
                    $('#modal-pengajuan-mail_id').val(mail.id);
                    $('#modal-pengajuan-title').text('Pengajuan ' + mail.name);
                    $('#modal-pengajuan').modal('show');
                }
            }else{
                Toast.fire({
                    icon: "error",
                    text: "Terjadi Kesalahan, " + res.message,
                });
            }
        });
    }

    function submitPengajuan(){
        const form = document.getElementById('modal-pengajuan-form');
        const btn = document.getElementById('submit-pengajuan');
        let formData = new FormData(form);
        $.ajax({
            url: "{{ route('pengajuan/surat.store') }}",
            type:"POST",
            data:formData,
            processData:false,
            contentType:false,
            success:function(res){
                if (res.status) {
                    Toast.fire({
                        icon:"success",
                        text:res.message,
                    });
                    form.reset();
                    setTimeout(() => {
                        window.location.href = "{{ route('surat/saya.index') }}";
                    }, 2000);
                }else{
                    Toast.fire({
                        icon:"error",
                        text:res.message,
                    });
                }
            },
            error:function(xhr){
                Toast.fire({
                    icon:"error",
                    text:(xhr.responseText() || "").slice(0,150),
                });
            }
        });
    }
</script>
@endpush
