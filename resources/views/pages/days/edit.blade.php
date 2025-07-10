@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <!-- Basic Layout -->
    <div class="card">
        <h5 class="card-header">Edit {{ $title ?? 'Hari' }}</h5>
        <div class="card-body">
            <form action="{{ route('master/hari.update', encrypt($item->id)) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="row mb-6">
                    <div class="col-sm-2">
                        <label class="form-label" for="order_number">Urutan</label>
                        <input type="number" class="form-control" name="order_number" id="order_number" min="1" value="{{ $item->order_number ?? 1 }}" required>
                    </div>
                    <div class="col-sm-10">
                        <label class="form-label" for="name">Nama Hari</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $item->name ?? '' }}" placeholder="Senin" required/>
                    </div>
                </div>

                <h5 class="border-top">Periode Pelajaran</h5>
                <div id="lesson_period_container">
                    @foreach ($item->lessonPeriods as $detail)
                        <div class="row mb-2" id="lesson_period_{{ $loop->iteration }}">
                            <div class="col-sm-2">
                                @if ($loop->first)
                                <label class="form-label" for="period_number_{{ $loop->iteration }}">Periode Ke</label>
                                @endif
                                <input type="number" class="form-control" name="period_number[]" id="period_number_{{ $loop->iteration }}" min="1" value="{{ $detail->period_number ?? 1 }}">
                            </div>
                            <div class="col-sm-10">
                                @if ($loop->first)
                                <label class="form-label" for="start">Rentang Waktu</label>
                                @endif
                                <div class="d-flex align-items-center">
                                    <div class="input-group">
                                        <input type="time" class="form-control" name="start[]" id="start_{{ $loop->iteration }}" value="{{ $detail->start ?? '00:00' }}"/>
                                        <span class="input-group-text" id="basic-addon13">-</span>
                                        <input type="time" class="form-control" name="end[]" id="end_{{ $loop->iteration }}" value="{{ $detail->end ?? '00:00' }}"/>
                                    </div>
                                    @if ($loop->first)
                                    <button type="button" class="btn btn-icon btn-sm btn-primary ms-1" onclick="addInput()"><i class="bx bx-plus"></i></button>
                                    @else
                                    <button type="button" class="btn btn-icon btn-sm btn-danger ms-1" onclick="removeInput(this)"><i class="bx bx-minus"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    </div>
  </div>


    @push('page-js')

        {{-- addInput --}}
        <script>
            let count = @json($item->lessonPeriods()->count());
            function addInput(){
                let newRow = `
                    <div class="row mb-2" id="lesson_period_${count}">
                        <div class="col-sm-2">
                            <input type="number" class="form-control" name="period_number[]" id="period_number_${count}" min="1" value="${count}">
                        </div>
                        <div class="col-sm-10">
                            <div class="d-flex align-items-center">
                                <div class="input-group">
                                    <input type="time" class="form-control" name="start[]" id="start_${count}" value=""/>
                                    <span class="input-group-text" id="basic-addon13">-</span>
                                    <input type="time" class="form-control" name="end[]" id="end_${count}" value=""/>
                                </div>
                                <button type="button" class="btn btn-icon btn-sm btn-danger ms-1" onclick="removeInput(this)"><i class="bx bx-minus"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                count++
                $('#lesson_period_container').append(newRow);
            }
            function removeInput(element) {
                $(element).closest('.row').remove();
            }
        </script>
    @endpush
@endsection