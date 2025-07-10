@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="row">
        {{-- Card Info --}}
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card text-center">
            <div class="card-body bg-primary rounded">
                <h6 class="text-muted text-white">Jumlah Siswa</h6>
                <h3 class="mb-0 text-white">{{ $total_siswa ?? 0 }}</h3>
            </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card text-center">
            <div class="card-body rounded bg-info">
                <h6 class="text-muted text-white">Jumlah Guru</h6>
                <h3 class="mb-0 text-white">{{ $total_guru ?? 0 }}</h3>
            </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card text-center">
            <div class="card-body bg-success">
                @canany(['guru', 'wali-kelas'])
                <h6 class="text-muted text-white">Kelas Aktif</h6>
                <h3 class="mb-0 text-white">{{ $total_dinamis ?? 0 }}</h3>
                @endcanany
                @can('kepsek')
                <h6 class="text-muted text-white">Jumlah Kelas</h6>
                <h3 class="mb-0 text-white">{{ $total_dinamis ?? 0 }}</h3>
                @endcan
                @can('admin')
                <h6 class="text-muted text-white">Jumlah User</h6>
                <h3 class="mb-0 text-white">{{ $total_dinamis ?? 0 }}</h3>
                @endcan
            </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card text-center">
            <div class="card-body bg-warning">
                <h6 class="text-muted text-white">Mata Pelajaran</h6>
                <h3 class="mb-0 text-white">{{ $total_mapel ?? 0 }}</h3>
            </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Grafik Rekapitulasi Kehadiran Siswa</h5>
            <small>Tahun : {{ $year ?? date('Y') }}</small>
        </div>
        <div class="card-body">
            <div id="attendanceChart" style="height: 300px;"></div>
        </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('vendor-js')    
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@push('page-js')
    <script>
        let json = @json($monthlyCount);
        let dt = Object.values(json);
        var options = {
            chart: {
            type: 'line',
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