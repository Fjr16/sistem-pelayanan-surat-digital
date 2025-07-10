<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function profile() {
        $arrJk = [
            'Laki-laki',
            'Perempuan',
        ];
        
        return view('pages.user.profile.index', [
            'arrJk' => $arrJk,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::all();
        $arrRole = [
            'Kepala Sekolah',
            'Guru',
            'Administrator',
        ];
        return view('pages.user.index', [
            'title' => 'Pengguna',
            'data' => $data,
            'arrRole' => $arrRole,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate([
                'name' => 'required|string',
                'username' => 'required|unique:users,username',
                'email' => 'required|unique:users,email',
                'role' => 'required|in:Kepala Sekolah,Guru,Administrator',
                'password' => 'required',
            ]);

            $data['password'] = Hash::make($request->password);

            User::create($data);

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
        try {
            DB::beginTransaction();
            $item = User::findOrFail($id);
            if (!$request['role']) {
                $request['role'] = $item->role;
            }
            $data = $request->validate([
                'name' => 'required|string',
                'username' => 'required|unique:users,username,' . $item->id,
                'email' => 'required|unique:users,email,' . $item->id,
                'role' => 'required|in:Kepala Sekolah,Guru,Administrator',
            ]);

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

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
        try {
            DB::beginTransaction();
            $item = User::findOrFail(decrypt($id));
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
        try {
            DB::beginTransaction();
            $item = User::withTrashed()->findOrFail(decrypt($id));
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
        try {
            DB::beginTransaction();
            $item = User::withTrashed()->findOrFail(decrypt($id));
            $item->teacher()->forceDelete();
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
