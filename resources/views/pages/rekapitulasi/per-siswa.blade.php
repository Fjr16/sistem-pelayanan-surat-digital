@extends('layout.main')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <!-- start -->
        <div class="card">
            <div class="card-header">
                <div class="">
                    <h5 class="mb-0">Data Siswa</h5>
                    <span class="fw-bold small">Jumlah : {{ $data->count() ?? '-' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm dataTable" id="dataSiswa">
                    <thead class="table-dark">
                        <tr class="text-nowrap">
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat / Tanggal Lahir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-secondary">
                        @foreach ($data as $item )
                            <tr>
                                <td>{{ $item->nisn ?? '-' }}</td>
                                <td>{{ $item->name ?? '-' }}</td>
                                <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                                <td>{{ $item->tempat_lhr ?? '-' }} / {{ $item->tanggal_lhr ?? '--/--/--' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-secondary text-white" onclick="openModalCreate('{{ $item->id }}')"> 
                                        <i class="bx bxs-file-export me-1"></i>
                                        Presensi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- end --}}
    </div>

    <!-- Modal create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xxl" role="document">
            <form action="{{ route('rekap/presensi/siswa.excel') }}" method="POST">
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
                                    @foreach ($listTahun as $year)
                                        @if (old('year') === $year)
                                            <option value="{{ $year }}" selected>{{ $year }}</option>
                                        @else
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="month_start">Bulan</label>
                            <div class="col-sm-4">
                                <select name="month_start" class="form-control" id="month_start" required>
                                    @foreach ($listBulan as $key => $month)
                                        @if (old('month_start') === $key)
                                            <option value="{{ $key }}" selected>{{ $month }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $month }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 text-center">-</div>
                            <div class="col-sm-4">
                                <select name="month_end" class="form-control" id="month_end" required>
                                    @foreach ($listBulan as $key => $month)
                                        @if (old('month_end') === $key)
                                            <option value="{{ $key }}" selected>{{ $month }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $month }}</option>
                                        @endif
                                    @endforeach
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
    @endpush
@endsection