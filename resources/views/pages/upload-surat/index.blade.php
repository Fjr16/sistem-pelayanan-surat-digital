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
            <div class="card-header">
                <h5>{{ $title ?? '-' }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm" id="tabel-data">
                        <thead class="table-dark">
                            <tr class="text-nowrap">
                                <th>Action</th>
                                <th>Nama Surat</th>
                                <th>Diajukan Pada</th>
                                <th>Diajukan Penduduk</th>
                                <th>Dibantu Petugas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal upload --}}
    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-labelledby="modalVerifikasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalVerifikasiLabel">Data Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="ringkasan-surat" class="mb-3"></div>

                <div class="card mb-3">
                    <div class="card-header fw-bold">Persyaratan</div>
                    <div class="card-body">
                        <div id="persyaratan-surat"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    @push('page-js')
    {{-- agar dapat membuat selector area pada pdf --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>

    <script>
        var table;
        let selectedArea = null;
        let currentPdf = null;
        let currentPage = 1;
        let scale = 1.5; // zoom PDF
        let overlay;

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:"{{ route('proses/surat/upload.show') }}",
                columns:[
                    {
                        data:'action',
                        name:'action',
                        orderable:false,
                        searchable:false
                    },
                    {
                        data:'mail_name',
                        name:'mails.name'
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
                        data:'status',
                        name:'status',
                        orderable:false,
                        'defaultContent' : '-'
                    },
                ],
                order:[
                    [2, 'desc']
                ]
            });
        });


        async function uploadSurat(incomingMailId){
            const { value: file } = await Swal.fire({
                title: "Upload Surat",
                input: "file",
                inputAttributes: {
                    "accept": "application/pdf",
                    "aria-label": "Upload your PDF file"
                },
                showCancelButton:true,
                cancelButtonText:"Kembali",
                cancelButtonColor: "#d33",
                confirmButtonColor: "#3085d6",
                inputValidator: (value) => {
                    if (!value) {
                        return "File tidak boleh kosong!";
                    }
                    if (value && value.type !== "application/pdf") {
                        return "Hanya file PDF yang diperbolehkan!";
                    }
                }
            });

            if (file) {
                const fileURL = URL.createObjectURL(file);
                Swal.fire({
                    title: "Petakan Peletakan TTD",
                    // html: `<iframe src="${fileURL}" width="100%" height="600px" style="border:none;"></iframe>`,
                    html: `
                        <div style="position:relative; width:100%; height:600px; overflow:auto;">
                            <canvas id="pdf-canvas"></canvas>
                            <canvas id="overlay-canvas" style="position:absolute; top:0; left:0;"></canvas>
                            <div class="mt-2 text-center">
                                <button id="prev-page" class="btn btn-sm btn-outline-primary">Prev</button>
                                <span id="page-info"></span>
                                <button id="next-page" class="btn btn-sm btn-outline-primary">Next</button>
                            </div>
                        </div>
                    `,
                    width: 900,
                    heightAuto: false,
                    showCancelButton:true,
                    cancelButtonText:"Batal",
                    showConfirmButton:true,
                    confirmButtonText:"Ya, Lanjutkan",
                    cancelButtonColor: "#d33",
                    confirmButtonColor: "#3085d6",
                    didOpen: () => {
                        renderPDF(fileURL);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!selectedArea) {
                            Swal.fire({
                                title: "Tempat Tanda Tangan ?",
                                text: "Area peletakkan tanda tangan tidak ditemukan !",
                                icon: "error"
                            });
                            return;
                        }
                        let formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('surat_pdf', file);
                        formData.append('incoming_mail_id', incomingMailId);
                        formData.append('obj_ttd', JSON.stringify(selectedArea));
                        formData.append('canvas_width', overlay.width);
                        formData.append('canvas_height', overlay.height);

                        $.ajax({
                            url:"{{ route('proses/surat/upload.store') }}",
                            type:"post",
                            data:formData,
                            contentType:false,
                            processData:false,
                            success:function(res){
                                if (res.status) {
                                    Toast.fire({
                                        icon:'success',
                                        text:res.message,
                                    });
                                }else{
                                    Toast.fire({
                                        icon:'error',
                                        text:res.message,
                                    });
                                }
                                table.ajax.reload();
                            },error:function(xhr){
                                Toast.fire({
                                    icon:'error',
                                    text:xhr.responseText().slice(0, 150),
                                });
                            }
                        })
                    }
                });
            }
        }

        // untuk render pdf dan selector area menggunakan pdf.js
        function renderPDF(url) {
            const canvas = document.getElementById("pdf-canvas");
            overlay = document.getElementById("overlay-canvas");
            const ctx = canvas.getContext("2d");

            pdfjsLib.getDocument(url).promise.then(pdf => {
                currentPdf = pdf;
                currentPage = 1;
                showPage(currentPage);

                document.getElementById("prev-page").addEventListener("click", ()=>{
                    if(currentPage <= 1) return;
                    currentPage--;
                    showPage(currentPage);
                });
                document.getElementById("next-page").addEventListener("click", ()=>{
                    if(currentPage >= pdf.numPages) return;
                    currentPage++;
                    showPage(currentPage);
                });
            });

            function showPage(pageNum){
                currentPdf.getPage(pageNum).then(page=>{
                    const viewport = page.getViewport({ scale });
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    overlay.width = viewport.width;
                    overlay.height = viewport.height;

                    const renderContext = { canvasContext: ctx, viewport };
                    page.render(renderContext);

                    document.getElementById("page-info").textContent = `Page ${pageNum} / ${currentPdf.numPages}`;

                    // aktifkan selection
                    enableSelection(overlay, pageNum);
                });
            }
        }

        function enableSelection(overlay, pageNum) {
            // hapus listener lama (opsional)
            overlay.onmousedown = null;
            overlay.onmousemove = null;
            overlay.onmouseup = null;

            let isDrawing = false;
            let startX, startY;
            let ctx = overlay.getContext("2d");

            overlay.addEventListener("mousedown", (e) => {
                isDrawing = true;
                startX = e.offsetX;
                startY = e.offsetY;
            });

            overlay.addEventListener("mousemove", (e) => {
                if (!isDrawing) return;
                let x = e.offsetX;
                let y = e.offsetY;
                let width = x - startX;
                let height = y - startY;

                ctx.clearRect(0, 0, overlay.width, overlay.height);
                ctx.strokeStyle = "red";
                ctx.lineWidth = 2;
                ctx.strokeRect(startX, startY, width, height);
            });

            overlay.addEventListener("mouseup", (e) => {
                isDrawing = false;
                let endX = e.offsetX;
                let endY = e.offsetY;
                selectedArea = {
                    page:pageNum,
                    x: Math.min(startX, endX),
                    y: Math.min(startY, endY),
                    width: Math.abs(endX - startX),
                    height: Math.abs(endY - startY)
                };
                // console.log("Area terpilih:", selectedArea);
            });
        }
        // end pdf selector and render

        function openModal(id) {
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
                                html += `<p><a href="/storage/${field.value}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bx bxs-file-image"></i> Lihat Gambar</a></p>`;
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

                // tampilkan modal
                $('#modalVerifikasi').modal('show');
            });
        }
    </script>
    @endpush
@endsection
