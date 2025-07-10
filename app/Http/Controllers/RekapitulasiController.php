<?php

namespace App\Http\Controllers;

use App\Exports\PresensiExport;
use App\Exports\RekapAkhirExport;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class RekapitulasiController extends Controller
{

    public function indexSiswa(){
        $data = Student::all();
        Carbon::setLocale('id');
        $listBulan = [];
        for($i = 1; $i <= 12; $i++){
            $listBulan[$i] = Carbon::createFromDate(null,$i,1)->translatedFormat('F');
        }
        return view('pages.rekapitulasi.per-siswa', [
            'title' => 'Rekap Presensi Per-Siswa',
            'data' => $data,
            'listBulan' => $listBulan,
            'listTahun' => range(date('Y'), 2020),
        ]);
    }

    public function presensiSiswaExcel(Request $request){
        try {
            $data = $request->validate([
                'student_id' => 'nullable|exists:students,id',
                'year' => 'required|integer',
                'month_start' => 'required|integer|min:1|max:12',
                'month_end' => 'required||integer|max:12|gte:month_start', //greater than equal
            ],[
                'month_end.gte' => 'Bulan yang dipilih tidak valid',
            ]);
    
            $startDate = now()->setYear((int) $request->year)->setMonth((int) $request->month_start)->startOfMonth();
            $endDate = now()->setYear((int) $request->year)->setMonth((int) $request->month_end)->endOfMonth();

    
            $item = Student::findOrFail($data['student_id']);
            $query = StudentAttendance::with([
                                            'lessonSchedule.teacher',
                                            'lessonSchedule.lessonPeriod',
                                            'student',
                                        ])
                                        ->whereBetween('tanggal', [$startDate, $endDate])
                                        ->orderBy('tanggal', 'asc');

            if ($item) {
                $query->where('student_id', $item->id);
            }
            $absensi = $query->get();
    
            $detail = $absensi->map(function ($item) {
                return collect([
                    'tanggal' => $item->tanggal,
                    'jam_pelajaran' => ($item->lessonSchedule->lessonPeriod->start ?? '-') . '-' . ($item->lessonSchedule->lessonPeriod->end ?? '-'),
                    'presensi' => $item->absensi,
                    'mata_pelajaran' => $item->lessonSchedule->teacher->subject->name ?? '-',
                    'guru' => $item->lessonSchedule->teacher->name ?? '-',
                ]);
            });

            $export = new PresensiExport($detail, (int) $request->year, (int) $request->month_start, (int) $request->month_end, $item->name);
            return Excel::download($export, 'Presensi-'. $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') .'_'. $item->name . '.xlsx');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexAkhir(){
        Carbon::setLocale('id');
        $listBulan = [];
        for($i = 1; $i <= 12; $i++){
            $listBulan[$i] = Carbon::createFromDate(null,$i,1)->translatedFormat('F');
        }
        return view('pages.rekapitulasi.rekap-akhir', [
            'title' => 'Rekap Presensi Akhir',
            'listBulan' => $listBulan,
            'listTahun' => range(date('Y'), 2020),
        ]);
    }

    public function presensiAkhirExcel(Request $request){
        try {
            $request->validate([
                'year' => 'required|integer',
                'month_start' => 'required|integer|min:1|max:12',
                'month_end' => 'required||integer|max:12|gte:month_start', //greater than equal
            ],[
                'month_end.gte' => 'Bulan yang dipilih tidak valid',
            ]);
    
            $startDate = now()->setYear((int) $request->year)->setMonth((int) $request->month_start)->startOfMonth();
            $endDate = now()->setYear((int) $request->year)->setMonth((int) $request->month_end)->endOfMonth();

    
            $query = StudentAttendance::with([
                                            'lessonSchedule.teacher',
                                            'lessonSchedule.teacher.subject',
                                            'lessonSchedule.lessonPeriod',
                                            'student',
                                        ])
                                        ->whereBetween('tanggal', [$startDate, $endDate])
                                        ->orderBy('tanggal', 'asc');
                                        
            $absensi = $query->get();

            $dataBaseStudent = $absensi->groupBy('student_id')->map(function ($items) {
                return collect([
                    'name' => $items->first()->student->name ?? '-',
                    'hadir' => $items->where('absensi', 'hadir')->count(),
                    'sakit' => $items->where('absensi', 'sakit')->count(),
                    'izin' => $items->where('absensi', 'izin')->count(),
                    'alfa' => $items->where('absensi', 'alfa')->count(),
                ]);
            });
            $dataBaseMapel = $absensi->groupBy(function ($q) {
                return $q->lessonSchedule->teacher->subject_id;
            })->map(function ($items) {
                return collect([
                    'mapel' => $items->first()->lessonSchedule->teacher->subject->name ?? '-',
                    'hadir' => $items->where('absensi', 'hadir')->count(),
                    'sakit' => $items->where('absensi', 'sakit')->count(),
                    'izin' => $items->where('absensi', 'izin')->count(),
                    'alfa' => $items->where('absensi', 'alfa')->count(),
                ]);
            });
            $dataBaseJk = $absensi->groupBy(function ($q) {
                return $q->student->jenis_kelamin;
            })->map(function ($items) {
                return collect([
                    'jenis_kelamin' => $items->first()->student->jenis_kelamin ?? '-',
                    'hadir' => $items->where('absensi', 'hadir')->count(),
                    'sakit' => $items->where('absensi', 'sakit')->count(),
                    'izin' => $items->where('absensi', 'izin')->count(),
                    'alfa' => $items->where('absensi', 'alfa')->count(),
                ]);
            });
            $dataBaseTingkatan = $absensi->groupBy(function ($q) {
                return $q->student->grade->tingkatan;
            })->map(function ($items) {
                return collect([
                    'tingkatan' => $items->first()->student->grade->tingkatan ?? '-',
                    'hadir' => $items->where('absensi', 'hadir')->count(),
                    'sakit' => $items->where('absensi', 'sakit')->count(),
                    'izin' => $items->where('absensi', 'izin')->count(),
                    'alfa' => $items->where('absensi', 'alfa')->count(),
                ]);
            });


            $export = new RekapAkhirExport($dataBaseStudent, $dataBaseMapel, $dataBaseJk, $dataBaseTingkatan);
            return Excel::download($export, 'rekap-'. $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.xlsx');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
