@extends('layout.main')

@push('page-css')
    <style>
        .card-status-title {
            min-height: 60px;     /* sesuaikan, kira-kira cukup untuk 2 baris */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
@endpush
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="card my-2 p-4">
            <div class="card-body bg-blue p-4 rounded">
                <h4 class="text-white m-0">Halo, {{ Auth::user()->name }}</h4>
                <h6 class="text-white m-0">Selamat Datang Di Aplikasi {{ config('app.name') ?? 'kami' }} @canany(['warga','petugas']), sudah siap melakukan pengajuan surat ? @endcanany</h6>
                @canany(['warga', 'petugas'])
                <a href="{{ route('pengajuan/surat.create') }}" class="btn btn-sm btn-white mt-3 text-dark"> <i class="bx bxs-send me-1"></i> Ajukan Sekarang</a>
                @endcanany
            </div>
        </div>
        @canany(['warga', 'sekretaris', 'wali-nagari'])
        <div class="card mb-2 p-4">
            <div class="card-body px-2 py-0">
                <div class="col-md-12 d-flex justify-content-between">
                    <h4 class="m-0 text-primary text-capitalized">Total Pengajuan</h4>
                    <h3 class="m-0 text-primary">{{ $totalPengajuan ?? 0 }} kali</h3>
                </div>
            </div>
        </div>
        @endcanany

        @canany(['petugas','sekretaris','wali-nagari'])
        {{-- Chart --}}
        <div class="card mb-2">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $data['title'] ?? '-' }}</h4>

                <div class="d-flex align-items-center">
                    <label for="year_filter" class="form-label mb-0 me-2">Tahun:</label>
                    <input type="text" name="year_filter" id="year_filter" value="{{ request('year') ?? $year }}" class="form-control form-control-sm" style="width: 100px;">
                </div>
            </div>
            <div class="card-body">
                <div id="attendanceChart" style="height: 300px;"></div>
            </div>
        </div>
        @endcanany

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Statistik Pengajuan Surat Berdasarkan Status Surat</h5>
                <div class="d-flex align-items-center">
                    <label for="month_filter" class="form-label mb-0 me-2">Bulan: {{ date('F / Y') }}</label>
                    {{-- <input type="text" class="form-control form-control-sm" name="month_filter" id="month_filter" value="{{ request('month_filter') }}" style="width: 150px;"> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($data['baseOnStatus'] as $item)
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card text-center h-100">
                        <div class="card-body {{ $item['color'] }} rounded d-flex flex-column justify-content-between">
                            <h6 class="text-muted fw-bold text-white card-status-title">
                                Surat Dengan Status <br>
                                <span class="text-uppercase ">{{ $item['label'] }}</span>
                            </h6>
                            <h3 class="mb-0 text-white">{{ $item['jumlah'] }}</h3>
                        </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @canany(['warga', 'sekretaris', 'wali-nagari'])
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Statistik Pengajuan Surat Berdasarkan Jenis Surat</h5>
                <div class="d-flex align-items-center">
                    <label for="month_filter" class="form-label mb-0 me-2">Bulan: {{ date('F / Y') }}</label>
                    {{-- <input type="text" class="form-control form-control-sm" name="month_filter" id="month_filter" value="{{ request('month_filter') }}" style="width: 150px;"> --}}
                </div>
            </div>
            <div class="card-body">
                @php
                $arrBgClass = ['bg-orange', 'bg-red', 'bg-blue', 'bg-green', 'bg-purple', 'bg-pink', 'bg-teal', 'bg-gray', 'bg-info'];
                if(!isset($data['baseOnMail'])){
                    $data['baseOnMail'] = [];
                }
                @endphp
                <div class="row">
                    @foreach ($data['baseOnMail'] as $item)
                    @php
                        $selectedBg = Arr::random($arrBgClass);
                    @endphp
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card text-center h-100">
                        <div class="card-body {{ $selectedBg }} rounded d-flex flex-column justify-content-between">
                            <h6 class="text-muted fw-bold text-white card-status-title">
                                <span class="text-uppercase ">{{ $item['name'] }}</span>
                            </h6>
                            <h3 class="mb-0 text-white">{{ $item['jumlah'] }}</h3>
                        </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endcanany
        @can('sekretaris')
        <div class="card mb-4">
            <div class="card-header">
                <h5>Statistik Pengguna Aplikasi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($data['baseOnRoles'] as $item)
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card text-center">
                        <div class="card-body {{ $item['color'] }} rounded">
                            <h6 class="text-muted fw-bold text-white">
                                <span class="text-uppercase ">{{ $item['label'] }}</span>
                            </h6>
                            <h3 class="mb-0 text-white">{{ $item['jumlah'] }}</h3>
                        </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endcan

    </div>
</div>
@endsection

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@push('page-js')
    <script>
        $('#year_filter').datepicker({
            format:"yyyy",
            viewMode:'years',
            minViewMode:'years'
        });
        $('#year_filter').on('change', function(){
            window.location.href = "{{ url('/dashboard') }}?year=" + $(this).val();
        });

        let json = @json($data['byMonth'] ?? null);
        let dt = Object.values(json);
        var options = {
            chart: {
                type: 'area',
                height: 300
            },
            series: [{
                name: 'Kehadiran',
                data: dt ?? 0,
            }],
            xaxis: {
                categories: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        }

        var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
        chart.render();
    </script>
@endpush
