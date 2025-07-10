@extends('layout.main')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <!-- start -->
        <div class="card">
            <div class="card-header">
                <div class="">
                    <h5 class="mb-0">{{ $title ?? 'Rekap Akhir' }}</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('rekap/presensi/akhir.excel') }}" method="POST">
                @csrf
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
                    <div class="col-sm-10 ms-auto">
                        <button type="submit" class="btn btn-primary btn-md"><i class="bx bxs-file-export"></i>Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- end --}}
    </div>
@endsection