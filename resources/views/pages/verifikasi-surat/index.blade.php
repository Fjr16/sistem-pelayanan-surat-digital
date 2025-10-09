@extends('layout.main')

@push('page-css')
<style>
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 6px 6px 0 0;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    .nav-tabs .nav-link:hover {
        background-color: rgba(0,0,0,0.05);
    }

    .nav-tabs .nav-link.active {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ $title ?? '-' }}</h5>
                <div class="">
                    <a href="{{ route('pengajuan/surat.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-mail-send me-2" style="font-size:20px;"></i> Ajukan Sekarang</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs nav-fill" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::pending->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-hourglass me-2"></i>
                            <span class="fw-semibold">Verifikasi</span>
                            <span class="badge bg-warning text-dark rounded-pill ms-2 text-white">{{ $countSurat->pendingCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::rejected->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-block me-2"></i>
                            <span class="fw-semibold">Ditolak</span>
                            <span class="badge bg-danger text-dark rounded-pill ms-2 text-white">{{ $countSurat->rejectedCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center px-4 py-2 rounded shadow-sm me-2"
                            id="tab-{{ \App\Enums\ProcessStatus::cancel->value }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content"
                            type="button"
                            role="tab">
                            <i class="bx bx-minus-circle me-2"></i>
                            <span class="fw-semibold">Dibatalkan</span>
                            <span class="badge bg-secondary text-dark rounded-pill ms-2 text-white">{{ $countSurat->cancelCount }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3 px-0">
                    <div class="tab-pane fade show active" id="content" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-sm" id="tabel-data">
                                <thead class="table-dark">
                                    <tr class="text-nowrap">
                                        <th>Action</th>
                                        <th>Nama Surat</th>
                                        <th>Diajukan Pada</th>
                                        <th>Diajukan Penduduk</th>
                                        <th>Dibantu Petugas</th>
                                        <th>Ket</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Verifikasi --}}
    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-labelledby="modalVerifikasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold" id="modalVerifikasiLabel">Verifikasi Surat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            {{-- Ringkasan Surat --}}
            <div id="ringkasan-surat" class="mb-3"></div>

            {{-- Persyaratan Dinamis --}}
            <div class="card mb-3">
                <div class="card-header fw-bold">Persyaratan</div>
                <div class="card-body">
                    <div id="persyaratan-surat"></div>
                </div>
            </div>

            {{-- Catatan Verifikasi --}}
            <div class="card">
                <div class="card-header fw-bold">Keterangan Penolakan (optional)</div>
                <div class="card-body">
                    <textarea id="keterangan-penolakan" class="form-control" rows="3"
                            placeholder="Tuliskan catatan penolakan pengajuan pembuatan surat penduduk"></textarea>
                </div>
            </div>

        </div>
        <div class="modal-footer"></div>
        </div>
    </div>
    </div>


    @push('page-js')
     {{-- cdn form render --}}
    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
    <script>
        var table;
        var status = "";

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                    url:"{{ route('proses/surat/verifikasi.show') }}",
                    data: function(d){
                        d.status = status;
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
                        data:'mail_name',
                        name:'mails.name',
                        searchable:true
                    },
                    {
                        data:'incoming_mails.created_at',
                        name:'incoming_mails.created_at'
                    },
                    {
                        data:'penduduk_name',
                        name:'pend.name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'petugas_name',
                        name:'pet.name',
                        'defaultContent' : '-'
                    },
                    {
                        data:'keterangan',
                        name:'keterangan',
                        'defaultContent' : '-'
                    },
                ],
                order:[
                    [2, 'desc']
                ]
            });

            $('#roleTabs button').on('click', function(){
                var tabId = $(this).attr('id');
                if (tabId === 'tab-all') {
                    status = "PENDING";
                }else{
                    status = tabId.replace('tab-', '');
                }
                table.ajax.reload();
            });
        });


        function verifikasi(incomingMailId, stts){
            Swal.fire({
                title:"Apakah Anda Yakin ?",
                showCancelButton: true,
                cancelButtonText: "Kembali",
                cancelButtonColor: "#d33",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Ya, Lanjutkan !"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:"{{ route('proses/surat/verifikasi.update') }}",
                        type:'POST',
                        data:{
                            _token:"{{ csrf_token() }}",
                            incoming_mail_id:incomingMailId,
                            status_update:stts,
                            keterangan:$('#keterangan-penolakan').val()
                        },
                        success:function(res){
                            if (res.status) {
                                Toast.fire({
                                    icon:'success',
                                    text:res.message,
                                });
                                $('#modalVerifikasi').modal('hide');
                                table.ajax.reload();
                            }else{
                                Toast.fire({
                                    icon:'error',
                                    text:res.message,
                                });
                            }
                        },error:function(xhr){
                            Toast.fire({
                                icon:'error',
                                text:xhr.responseText().slice(0, 150),
                            });
                        }
                    });
                }
            });
        }

        function openVerifikasiModal(id) {
            $.get("{{ url('proses/surat/verifikasi/getDetail') }}/" + id, function(res) {
            // Isi ringkasan
                $('#ringkasan-surat').html(`
                    <strong>${res.mail_name}</strong><br>
                    Pemohon: ${res.penduduk_name}<br>
                    Dibantu Petugas: ${res.petugas_name ?? '-'}<br>
                    Diajukan Pada: ${res.created_at}
                `);

                let html = "";
                if (res.requirements) {
                    res.requirements.forEach(field => {
                        // Badge status
                        let statusBadge = "";
                        if (field.is_required) {
                            statusBadge = field.is_filled
                                ? `<span class="badge bg-success ms-2"><i class="bx bx-check me-1"></i> Lengkap</span>`
                                : `<span class="badge bg-danger ms-2"><i class="bx bx-x me-1"></i> Wajib diisi</span>`;
                        } else {
                            statusBadge = field.is_filled
                                ? `<span class="badge bg-success ms-2"><i class="bx bx-check me-1"></i> Terisi</span>`
                                : `<span class="badge bg-secondary ms-2"><i class="bx bx-info-circle me-1"></i> Optional</span>`;
                        }
    
                        // Label
                        html += `<div class="mb-3">
                                    <label class="fw-bold d-flex justify-content-between border-bottom mb-2 pb-2">
                                        <div>
                                            ${field.label}${field.is_required ? ' *' : ''} 
                                        </div>
                                        ${statusBadge}
                                    </label>
                                `;
    
                        // Tipe tampilan
                        if (field.type === "file") {
                            if (field.value) {
                                // File link
                                html += `<p><a href="/storage/${field.value}" target="_blank" class="btn btn-sm btn-outline-primary">📄 Lihat Dokumen</a></p>`;
                                // Bisa juga preview langsung PDF
                                if (field.value.endsWith(".pdf")) {
                                    html += `<iframe src="/storage/${field.value}" width="100%" height="300px" style="border:none;"></iframe>`;
                                }
                            } else {
                                html += `<p class="text-muted">Tidak ada file</p>`;
                            }
                        } else if (Array.isArray(field.value)) {
                            if (field.value.length > 0) {
                                html += "<ul>";
                                field.value.forEach(v => {
                                    html += `<li>${v}</li>`;
                                });
                                html += "</ul>";
                            } else {
                                html += `<p class="text-muted">-</p>`;
                            }
                        } else {
                            html += `<p class="text-muted">${field.value ?? "-"}</p>`;
                        }
    
                        html += `</div>`;
                    });
                }else{
                    html ="<p class='text-danger fst-italic'>Tidak Ada Persayaratan Untuk Surat Ini</p>" 
                }
                
                $('#persyaratan-surat').html(html);

                // simpan ID ke modal
                $('#modalVerifikasi .modal-footer').html(`
                    <button type="button" class="btn btn-success" onclick="verifikasi(${id},'terima')"><i class="bx bx-check me-1"></i> Verifikasi</button>
                    <button type="button" class="btn btn-danger" onclick="verifikasi(${id},'tolak')"><i class="bx bx-x me-1"></i> Tolak</button>
                `);

                // tampilkan modal
                $('#modalVerifikasi').modal('show');
            });
        }

    </script>
    @endpush
@endsection
