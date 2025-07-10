@extends('layout.main')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <!-- start -->
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="">
                <h5 class="mb-2">{{ $title ?? 'Presensi Siswa' }}</h5>
                <p class="small mb-1">{{ $today ??  Carbon\Carbon::now()->translatedFormat('l, Y-m-d') }}</p>
                <p class="small mb-1">Waktu : {{ ($item->lessonPeriod->start ?? '-') . '-' . ($item->lessonPeriod->end ?? '-') }}</p>
                <p class="small mb-5">Jam Ke : {{ $item->lessonPeriod->period_number ?? '-' }}</p>
                <p class="fw-bold mb-0">Mapel : {{ $item->teacher->subject->name ?? '-' }}</p>
            </div>
            <div class="">
                <h3>{{ ($item->grade->tingkatan ?? '-') . ' ' . ($item->grade->code ?? '-') }}</h3>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('menu/saya/kelas-aktif.presensiStore') }}" method="POST">
                @csrf
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm">
                    <caption class="mt-4">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('menu/saya/kelas-aktif.index') }}" class="btn btn-md btn-danger me-2"><i class="bx bx-left-arrow"></i> Kembali</a>
                            <button class="btn btn-md btn-primary" type="submit"><i class="bx bx-save"></i> Simpan</button>
                        </div>
                    </caption>
                    <thead class="table-dark">
                        <tr class="text-nowrap">
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Presensi *</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item->grade->students as $siswa )
                            <tr>
                                <td class="text-dark">
                                    <input type="hidden" name="lesson_schedule_id" value="{{ $item->id ?? '' }}">
                                    <input type="hidden" name="student_id[]" id="student_id_{{ $loop->iteration }}" value="{{ $siswa->id ?? '' }}">
                                    <input type="hidden" name="tanggal" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                    {{ $siswa->name ?? '-' }}
                                </td>
                                <td class="text-dark">{{ $siswa->jenis_kelamin ?? '' }}</td>
                                <td class="text-dark">
                                    <select name="absensi[]" id="absensi_{{ $loop->iteration }}" class="form-select form-select-sm">
                                        @foreach ($optionsPresensi as $presensi)
                                            @if ($siswa->studentAttendances->where('tanggal', Carbon\Carbon::now()->format('Y-m-d'))->where('lesson_schedule_id', $item->id)->pluck('absensi')->first() == $presensi)
                                                <option value="{{ $presensi }}" selected class="text-capitalize">{{ $presensi }}</option>
                                            @else
                                                <option value="{{ $presensi }}" class="text-capitalize">{{ $presensi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-dark">
                                    <textarea name="keterangan[]" class="form-control" id="keterangan_{{ $loop->iteration }}" rows="1" placeholder="Keterangan siswa / siswi">{!! $siswa->studentAttendances->where('tanggal', Carbon\Carbon::now()->format('Y-m-d'))->where('lesson_schedule_id', $item->id)->pluck('keterangan')->first() !!}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
    {{-- end --}}
</div>

    

    @push('page-js')
        <script>
            {{-- redirect to page presensi create --}}
            function presensiSiswa(element){
                window.location = element.dataset.href;
            }
        </script>

        {{-- perfect scrollbar --}}
        <script>
            new PerfectScrollbar(document.getElementById('scrollbar-jadwal-kelas'), {
                wheelPropagation: false,
                useBothWheelAxes: true,
            });
        </script>
    @endpush
@endsection