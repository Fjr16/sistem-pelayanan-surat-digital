<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $arrJk = [
            'Laki-laki',
            'Perempuan',
        ];
        $data = Student::all();
        return view('pages.student.index', [
            'title' => 'Murid',
            'data' => $data,
            'arrJk' => $arrJk
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        try {
            DB::beginTransaction();
            $data = $request->validate([
                'nisn' => 'required|unique:students,nisn',
                'name' => 'required|string|max:100',
                'tempat_lhr' => 'nullable|string|max:100',
                'tanggal_lhr' => 'nullable|date',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            ]);

            Student::create($data);

            DB::commit();
            return back()->with('success', 'Berhasil Disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('admin');

        try {
            DB::beginTransaction();
            $item = Student::findOrFail($id);
            $data = $request->validate([
                'nisn' => 'required|unique:students,nisn,'.$item->id,
                'name' => 'required|string|max:100',
                'tempat_lhr' => 'nullable|string|max:100',
                'tanggal_lhr' => 'nullable|date',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            ]);

            $item->update($data);

            DB::commit();
            return back()->with('success', 'Berhasil Diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('admin');

        try {
            DB::beginTransaction();
            $item = Student::findOrFail(decrypt($id));
            $item->delete();

            DB::commit();
            return back()->with('success', 'Berhasil Dihapus');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }

    public function restore(Request $request, $id) {
        $this->authorize('admin');

        try {
            DB::beginTransaction();
            $item = Student::withTrashed()->findOrFail(decrypt($id));
            $item->restore();

            DB::commit();
            return back()->with('success', 'Berhasil Dipulihkan');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }

    public function forceDelete($id) {
        $this->authorize('admin');

        try {
            DB::beginTransaction();
            $item = Student::withTrashed()->findOrFail(decrypt($id));
            $item->studentAttendances()->delete();
            $item->forceDelete();

            DB::commit();
            return back()->with('success', 'Berhasil Dihapus Permanen');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }
}
