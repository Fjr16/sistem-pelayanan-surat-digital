@extends('layout.main')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
         <!-- Judul Halaman -->
        <div class="col-md-12 mb-3 text-center">
            <h4 class="fw-bold text-primary mb-0">{{ $title }}</h4>
            <small class="text-muted">Gunakan filter di bawah untuk mempersempit pencarian data</small>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Jenis Surat -->
                        <div class="col-md-4">
                            <label for="mail_id" class="form-label fw-semibold">Jenis Surat</label>
                            <select id="mail_id" name="mail_id" class="form-select">
                                <option value="" @selected(!old("mail_id"))>Semua</option>
                                @foreach ($mails as $mail)
                                    <option value="{{ $mail->id }}" @selected(old('mail_id') == $mail->id)>{{ $mail->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Surat -->
                        <div class="col-md-3">
                            <label for="status_surat" class="form-label fw-semibold">Status Surat</label>
                            <select id="status_surat" name="status_surat" class="form-select">
                                <option value="" @selected(!old("status_surat"))>Semua</option>
                                @foreach ($status as $stts)
                                    <option value="{{ $stts->value }}" @selected(old('status_surat') == $stts->value)>{{ $stts->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-2">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Awal</label>
                            <input type="date" id="start_date" name="start_date" class="form-control">
                        </div>

                        <!-- End Date -->
                        <div class="col-md-2">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="date" id="end_date" name="end_date" class="form-control">
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="col-md-1">
                            <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <!-- Search Box -->
                    <div class="col-12 col-md-10">
                        <input type="text" class="form-control form-control-md"
                            name="search_filter"
                            placeholder="Filter berdasarkan nama, NIK, atau WA pemohon">
                    </div>

                    <!-- Export Buttons -->
                    <div class="col-12 col-md-2 d-flex justify-content-md-end justify-content-start">
                        <button id="btnExport" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 w-100 w-md-auto">
                            <i class="bi bi-file-earmark-excel fs-5"></i>
                            <span>Export Excel</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm table-striped table-hover align-middle" id="tabel-laporan">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Pemohon</th>
                                <th>NIK Pemohon</th>
                                <th>WA Pemohon</th>
                                <th>Jenis Surat</th>
                                <th>No. Surat</th>
                                <th>Diajukan</th>
                                <th>Disahkan</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Surat</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xxl" role="document">
            <form action="" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Filter Rekapitulasi Presensi</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <input type="hidden" name="student_id" id="student_id">
                            <label class="col-sm-2 col-form-label" for="year">Tahun</label>
                            <div class="col-sm-10">
                                 <select name="year" class="form-control" id="year" required>
                                    {{-- @foreach ($listTahun as $year)
                                        @if (old('year') === $year)
                                            <option value="{{ $year }}" selected>{{ $year }}</option>
                                        @else
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endif
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="month_start">Bulan</label>
                            <div class="col-sm-4">
                                <select name="month_start" class="form-control" id="month_start" required>
                                    {{-- @foreach ($listBulan as $key => $month)
                                        @if (old('month_start') === $key)
                                            <option value="{{ $key }}" selected>{{ $month }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $month }}</option>
                                        @endif
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-sm-2 text-center">-</div>
                            <div class="col-sm-4">
                                <select name="month_end" class="form-control" id="month_end" required>
                                    {{-- @foreach ($listBulan as $key => $month)
                                        @if (old('month_end') === $key)
                                            <option value="{{ $key }}" selected>{{ $month }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $month }}</option>
                                        @endif
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Unduh</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('page-js')
        <script>
            function openModalCreate(studentId){
                const modal = document.getElementById('createModal');
                modal.querySelector('#student_id').value = studentId;
                const modalCreate = new bootstrap.Modal(modal);
                modalCreate.show()
            }
        </script>
        <script>
            var table;
            var status,stDate,enDate,mailId;
            var filter_column = '';

            $(document).ready(function(){
                table = $('#tabel-laporan').DataTable({
                    processing:true,
                    serverSide:true,
                    searching:false,
                    ajax:{
                        url:"{{ route('export.show') }}",
                        data:function(d){
                            d.status_surat = status;
                            d.end_date = enDate;
                            d.start_date = stDate;
                            d.mail_id = mailId;
                            // d.search_filter = $('input[name="search_filter"]').val();
                            d.search_filter = filter_column;
                        }
                    },
                    columns:[
                        { data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false },
                        {
                            data:'nama_pemohon',
                            name:'nama_pemohon',
                            'defaultContent':'-'
                        },
                        {
                            data:'nik_pemohon',
                            name:'nik_pemohon',
                            'defaultContent':'-'
                        },
                        {
                            data:'wa_pemohon',
                            name:'wa_pemohon',
                            'defaultContent':'-'
                        },
                        {
                            data:'mail_name',
                            name:'mail_name',
                            searchable:false,
                            'defaultContent':'-'
                        },
                        {
                            data:'letter_number',
                            name:'letter_number',
                            searchable:false,
                            'defaultContent':'-'
                        },
                        {
                            data:'created_at',
                            name:'created_at',
                            searchable:false,
                            'defaultContent':'-'
                        },
                        {
                            data:'updated_at',
                            name:'updated_at',
                            searchable:false,
                            'defaultContent':'-'
                        },
                        {
                            data:'status',
                            name:'status',
                            searchable:false,
                        },
                        {
                            data:'keterangan',
                            name:'keterangan',
                            orderable:false,
                            searchable:false,
                            'defaultContent':'-'
                        },
                        {
                            data:'aksi',
                            name:'aksi',
                            orderable:false,
                            searchable:false,
                        },
                    ],
                    order:[
                        [1,'asc']
                    ]
                });

                $('input[name="search_filter"]').on('keyup change', function(){
                    filter_column = $(this).val();
                    table.draw();
                });

                $('#btnFilter').on('click',function () {
                    status = $('#status_surat').val();
                    stDate = $('#start_date').val();
                    enDate = $('#end_date').val();
                    mailId = $('#mail_id').val();
                    if (!stDate || !enDate) {
                        Swal.fire({
                            icon:'error',
                            text:'Rentang tanggal harus diisi !'
                        });
                        return;
                    }
                    table.ajax.reload();
                });

                $('#btnExport').on('click', function(){
                    let params = {
                        status_surat: status,
                        start_date: stDate,
                        end_date: enDate,
                        mail_id: mailId,
                        search_filter: filter_column,
                    }

                    let query = $.param(params);

                    window.location.href = "{{ route('export.download') }}?" + query;
                })


                const startDate = document.getElementById("start_date");
                const endDate   = document.getElementById("end_date");

                startDate.addEventListener("change", function(){
                    if(startDate.value){
                        let start = new Date(startDate.value);

                        // Tentukan batas 30 hari setelah start
                        let maxEnd = new Date(start);
                        maxEnd.setDate(maxEnd.getDate() + 30);

                        // Atur min dan max untuk end_date
                        endDate.min = startDate.value;
                        endDate.max = maxEnd.toISOString().split("T")[0];

                        // Reset end_date kalau tidak valid
                        if(endDate.value){
                            let end = new Date(endDate.value);
                            if(end < start || end > maxEnd){
                                endDate.value = "";
                            }
                        }
                    } else {
                        endDate.removeAttribute("min");
                        endDate.removeAttribute("max");
                    }
                });
            });

            // function showSurat();

        </script>
    @endpush
@endsection
