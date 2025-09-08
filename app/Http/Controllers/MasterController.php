<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Mail;
use App\Models\MailCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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

    /* controller master data Penduduk */
    public function indexMail(){
        $data = Mail::all();
        return view('pages.mail.index', [
            'title' => 'Jenis Surat',
            'data' => $data,
        ]);
    }
    public function storeMail(Request $request) {
        return $request->all();
        // DB::beginTransaction();
        // try {
        //     $data = $request->validate([
        //         'name' => [
        //             'required',
        //             'string',
        //             Rule::unique('subjects')->where(function($query) use ($request) {
        //                 return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
        //             }),
        //         ],
        //         'code' => [
        //             'required',
        //             'string',
        //             Rule::unique('subjects')->where(function($query) use ($request) {
        //                 return $query->whereRaw('LOWER(code) = ?', [strtolower($request->name)]);
        //             }),
        //         ],
        //     ], [
        //         'name.required' => 'Nama mapel harus diisi',
        //         'name.string' => 'Format Data Tidak Valid',
        //         'name.unique' => 'nama mapel Telah digunakan',
        //         'code.required' => 'Kode mapel harus diisi',
        //         'code.string' => 'format data Tidak Valid',
        //         'code.unique' => 'kode mapel Telah digunakan',
        //     ]);
            
        //     Mail::create($data);
            
        //     DB::commit();
        //     return back()->with('success', 'Data Berhasil Disimpan');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        // } catch (ModelNotFoundException $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        // } catch (ValidationException $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        // }
    }
    public function updatePenduduk(Request $request, $id) {
        DB::beginTransaction();
        try {
            $item = Citizen::findOrFail($id);
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
    public function destroyPenduduk($id) {
        try {
            $item = Citizen::findOrFail(decrypt($id));
            $item->delete();
            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    /* End controller master data penduduk */

    /* controller master data Surat */
    public function indexJenisSurat(){
        $data = MailCategory::all();
        $arrTingkatan =  ['X', 'XI', 'XII'];
        return view('pages.grades.index', [
            'title' => 'Kelas',
            'data' => $data,
            'arrTingkatan' => $arrTingkatan,
        ]);
    }
    public function storeJenisSurat(Request $request) {
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

            $checkExists = MailCategory::where('code', $request->code)->where('tingkatan', $request->tingkatan)->first();
            if ($checkExists) {
                DB::rollBack();
                return back()->with('error', 'Terjadi Kesalahan: kode dan nama kelas telah digunakan !');
            }
            
            MailCategory::create($data);
            
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
    public function updateJenisSurat(Request $request, $id) {
        DB::beginTransaction();
        try {
            $item = MailCategory::findOrFail($id);
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

            $checkExists = MailCategory::where('code', $request->code)
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
    public function destroyJenisSurat($id) {
        try {
            $item = MailCategory::findOrFail(decrypt($id));
            $item->delete();
            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
    /* End controller master data Surat */
}
