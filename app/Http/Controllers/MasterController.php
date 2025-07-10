<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Grade;
use App\Models\LessonPeriod;
use App\Models\LessonSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MasterController extends Controller
{
    // start dashboard
    public function dashboard() {
        $year = now()->year;
        $startDate = now()->setYear((int) $year)->setMonth(1)->startOfMonth();
        $endDate = now()->setYear((int) $year)->setMonth(12)->endOfMonth();
                                    
        $totalDynamic = User::count();
        return view('pages.dashboard', [
            'total_siswa' => 10,
            'total_dinamis' => $totalDynamic,
            'total_guru' => 20,
            'total_mapel' => 50,
            'monthlyCount' => [20,40,50],
            'year' => $year
        ]);
    }
    // end dashboard
    /* controller master data hari */
    public function indexDays() {
        $data = Day::all();
        return view('pages.days.index', [
            'title' => 'Hari',
            'data' => $data, 
        ]);
    }
    public function storeDays(Request $request) {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                ],
                'order_number' => ['required', 'unique:days,order_number'],
            ], [
                'name.required' => 'Nama hari harus diisi',
                'order_number.required' => 'Urutan Hari harus diisi',
                'order_number.unique' => 'gunakan no urut lain',
                'name.string' => 'Format Data Tidak Valid',
            ]);

             // Cek apakah nama hari sudah ada (case-insensitive)
            if (Day::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->exists()) {
                DB::rollBack();
                return back()->with('error', 'Data Hari Telah Ada')->withInput();
            }
            
            $data2 = $request->validate([
                'period_number' => 'required|array',
                'period_number.*' => 'required',
                'start' => 'required|array',
                'start.*' => 'required',
                'end' => 'required|array',
                'end.*' => 'required',
            ], [
                'period_number.*.required' => 'Periode ke tidak boleh kosong',
                'start.*.required' => 'waktu mulai tidak boleh kosong',
                'end.*.required' => 'waktu selesai tidak boleh kosong',
            ]);

            $conflicts = [];

            foreach ($data2['period_number'] as $key => $periodNumber) {
                $start = $data2['start'][$key];
                $end = $data2['end'][$key];

                $conflict = LessonPeriod::where(function ($query) use ($periodNumber, $start, $end){
                                        $query->where('period_number', $periodNumber)
                                                ->OrwhereBetween('start', [$start, $end]) //start lama berada diantara jadwal baru
                                                ->orWhereBetween('end', [$start, $end]) //end lama berada diantara jadwal baru
                                                ->orWhere(function ($query) use ($start, $end) {   //atau ketika jadwal lama melingkupi jadwal baru mis: jw baru : 08:00 - 09:00 dan jw lama : 07:00 - 10:00
                                                    $query->where('start', '<=', $start)
                                                ->where('end', '>=', $end);
                                        });
                                    })->whereHas('day', function($query) use ($data){
                                        $query->whereRaw('LOWER(name) = ?', [strtolower($data['name'])]);
                                    })->first();
                
                if($conflict){
                    $conflicts[] = $conflict;
                }
            }
            if (!empty($conflicts)) {
                DB::rollBack();
                return back()->with('error', 'Kesalahan: Terdapat beberapa jadwal yang bentrok')->withInput();
            }
            

            $item = Day::create($data);
            foreach ($data2['period_number'] as $key => $period) {
                LessonPeriod::create([
                    'day_id' => $item->id,
                    'period_number' => $period,
                    'start' => $data2['start'][$key],
                    'end' => $data2['end'][$key],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function editDays($id) {
        $item = Day::findOrFail(decrypt($id));
        return view('pages.days.edit', [
            'title' => 'Hari',
            'item' => $item, 
        ]);
    }
    public function updateDays(Request $request, $id) {
        DB::beginTransaction();
        try {
            $item = Day::findOrFail(decrypt($id));

            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                ],
                'order_number' => ['required', 'unique:days,order_number,' . $item->id],
            ], [
                'name.required' => 'Nama hari harus diisi',
                'order_number.required' => 'Urutan Hari harus diisi',
                'order_number.unique' => 'gunakan no urut lain',
                'name.string' => 'Format Data Tidak Valid',
            ]);

             // Cek apakah nama hari sudah ada (case-insensitive)
            if (Day::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->where('id', '!=', $item->id)->exists()) {
                DB::rollBack();
                return back()->with('error', 'Nama Hari Telah digunakan')->withInput();
            }
            
            $data2 = $request->validate([
                'period_number' => 'required|array',
                'period_number.*' => 'required',
                'start' => 'required|array',
                'start.*' => 'required',
                'end' => 'required|array',
                'end.*' => 'required',
            ], [
                'period_number.*.required' => 'Periode ke tidak boleh kosong',
                'start.*.required' => 'waktu mulai tidak boleh kosong',
                'end.*.required' => 'waktu selesai tidak boleh kosong',
            ]);

            // hapus data sebelumya
            $item->lessonPeriods()->delete();

            $conflicts = [];

            foreach ($data2['period_number'] as $key => $periodNumber) {
                $start = $data2['start'][$key];
                $end = $data2['end'][$key];

                $conflict = LessonPeriod::where('day_id', $item->id)
                                        ->where(function ($query) use ($periodNumber, $start, $end){
                                        $query->where('period_number', $periodNumber)
                                                ->OrwhereBetween('start', [$start, $end]) //start lama berada diantara jadwal baru
                                                ->orWhereBetween('end', [$start, $end]) //end lama berada diantara jadwal baru
                                                ->orWhere(function ($query) use ($start, $end) {   //atau ketika jadwal lama melingkupi jadwal baru mis: jw baru : 08:00 - 09:00 dan jw lama : 07:00 - 10:00
                                                    $query->where('start', '<=', $start)
                                                ->where('end', '>=', $end);
                                        });
                                    })->first();
                
                if($conflict){
                    $conflicts[] = $conflict;
                }
            }
            if (!empty($conflicts)) {
                DB::rollBack();
                return back()->with('error', 'Kesalahan: Terdapat beberapa jadwal yang bentrok')->withInput();
            }
            

            foreach ($data2['period_number'] as $key => $period) {
                LessonPeriod::create([
                    'day_id' => $item->id,
                    'period_number' => $period,
                    'start' => $data2['start'][$key],
                    'end' => $data2['end'][$key],
                ]);
            }
            
            $item->update($data);
            
            DB::commit();
            return redirect()->route('master/hari.index')->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyDays($id) {
        try {
            $item = Day::findOrFail(decrypt($id));
            $item->lessonPeriods()->delete();
            $item->delete();

            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    /* End controller master data hari */ 

    /* controller master data mata pelajaran */
    public function indexSubject(){
        $data = Subject::all();
        return view('pages.subject.index', [
            'title' => 'Mata Pelajaran',
            'data' => $data,
        ]);
    }
    public function storeSubject(Request $request) {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('subjects')->where(function($query) use ($request) {
                        return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
                    }),
                ],
                'code' => [
                    'required',
                    'string',
                    Rule::unique('subjects')->where(function($query) use ($request) {
                        return $query->whereRaw('LOWER(code) = ?', [strtolower($request->name)]);
                    }),
                ],
            ], [
                'name.required' => 'Nama mapel harus diisi',
                'name.string' => 'Format Data Tidak Valid',
                'name.unique' => 'nama mapel Telah digunakan',
                'code.required' => 'Kode mapel harus diisi',
                'code.string' => 'format data Tidak Valid',
                'code.unique' => 'kode mapel Telah digunakan',
            ]);
            
            Subject::create($data);
            
            DB::commit();
            return back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    public function updateSubject(Request $request, $id) {
        DB::beginTransaction();
        try {
            $item = Subject::findOrFail($id);
            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                    Rule::unique('subjects')->ignore($id)->where(function($query) use ($request) {
                        return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
                    }),
                ],
                'code' => [
                    'required',
                    'string',
                    Rule::unique('subjects')->ignore($id)->where(function($query) use ($request) {
                        return $query->whereRaw('LOWER(code) = ?', [strtolower($request->name)]);
                    }),
                ],
            ], [
                'name.required' => 'Nama mapel harus diisi',
                'name.string' => 'Format Data Tidak Valid',
                'name.unique' => 'nama mapel Telah digunakan',
                'code.required' => 'Kode mapel harus diisi',
                'code.string' => 'format data Tidak Valid',
                'code.unique' => 'kode mapel Telah digunakan',
            ]);
            
            $item->update($data);
            
            DB::commit();
            return back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    public function destroySubject($id) {
        try {
            $item = Subject::findOrFail(decrypt($id));
            $item->delete();
            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    /* End controller master data hari */

    /* controller master data mata pelajaran */
    public function indexGrade(){
        $data = Grade::all();
        $arrTingkatan =  ['X', 'XI', 'XII'];
        $teachers = Teacher::whereDoesntHave('grade')->get();
        $teacherAll = Teacher::all();
        return view('pages.grades.index', [
            'title' => 'Kelas',
            'data' => $data,
            'arrTingkatan' => $arrTingkatan,
            'teachers' => $teachers,
            'teacherAll' => $teacherAll,
        ]);
    }
    public function storeGrade(Request $request) {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'code' => ['required','string'],
                'tingkatan' => ['required','string'],
                'teacher_id' => [
                    'required',
                    'exists:teachers,id',
                    'unique:grades,teacher_id',
            ],
            ], [
                'tingkatan.required' => 'Tingkatan harus diisi',
                'tingkatan.string' => 'Format Data Tidak Valid',
                'code.required' => 'Kode Kelas harus diisi',
                'code.string' => 'format data Tidak Valid',
                'teacher_id.required' => 'Wali Kelas Harus Dipilih',
                'teacher_id.exists' => 'Wali Kelas Tidak ditemukan',
                'teacher_id.unique' => 'Guru Telah telah memiliki kelas',
            ]);

            $checkExists = Grade::where('code', $request->code)->where('tingkatan', $request->tingkatan)->first();
            if ($checkExists) {
                DB::rollBack();
                return back()->with('error', 'Terjadi Kesalahan: kode dan nama kelas telah digunakan !');
            }
            
            Grade::create($data);
            
            DB::commit();
            return back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    public function updateGrade(Request $request, $id) {
        DB::beginTransaction();
        try {
            $item = Grade::findOrFail($id);
            $data = $request->validate([
                'code' => ['required','string'],
                'tingkatan' => ['required','string'],
                'teacher_id' => [
                    'required',
                    'exists:teachers,id',
                    'unique:grades,teacher_id,' . $item->id,
            ],
            ], [
                'tingkatan.required' => 'Tingkatan harus diisi',
                'tingkatan.string' => 'Format Data Tidak Valid',
                'code.required' => 'Kode Kelas harus diisi',
                'code.string' => 'format data Tidak Valid',
                'teacher_id.required' => 'Wali Kelas Harus Dipilih',
                'teacher_id.exists' => 'Wali Kelas Tidak ditemukan',
                'teacher_id.unique' => 'Guru Telah telah memiliki kelas',
            ]);

            $checkExists = Grade::where('code', $request->code)
                            ->where('tingkatan', $request->tingkatan)
                            ->whereNot('id', $item->id)
                            ->first();

            if ($checkExists) {
                DB::rollBack();
                return back()->with('error', 'Terjadi Kesalahan: kode dan nama kelas telah digunakan !');
            }
            
            $item->update($data);
            
            DB::commit();
            return back()->with('success', 'Data Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    public function destroyGrade($id) {
        try {
            $item = Grade::findOrFail(decrypt($id));
            $item->delete();
            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    /* End controller master data hari */
}
