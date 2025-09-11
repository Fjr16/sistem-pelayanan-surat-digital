<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function getData(){
        $roleId = request()->get('role_id');
        $data = User::with('role')
        ->when($roleId, function($q) use ($roleId){
            return $q->where('role_id', $roleId);
        });

        return DataTables::of($data)
            ->addIndexColumn()
            // menambahkan kolom aksi
            ->addColumn('action', function($item) {
                $editBtn = '<button type="button" class="btn btn-sm btn-warning text-white btn-icon me-1" onclick="openModalEdit(' . $item->id. ')"><i class="bx bx-edit"></i></button>';
                $deleteBtn = '<form id="deleteForm-' . $item->id . '" action="' . route('master/aktor/pengguna.destroy', encrypt($item->id)) . '" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn btn-sm btn-icon btn-danger text-white" onclick="confirmDelete(' . $item->id . ')"><i class="bx bx-trash"></i></button>
                            </form>';
                return $editBtn . $deleteBtn;
            })
            ->addColumn('ttl', function($item){
                $tempat = $item->tempat_lhr ?? '-';
                $tgl = $item->tanggal_lhr ?? '00-00-00';
                return $tempat . ', ' . $tgl;
            })
            // mengubah format tanggal
            ->editColumn('is_active', function($item){
                $bg = $item->i_active ? 'success' : 'danger';
                $icon = $item->i_active ? 'check' : 'x';
                return '<span class="p-0 badge bg-'. $bg . '"><i class="p-0 bx bx-'. $icon .'" style="font-size:30px;"></i></span>';
            })
            // agar kolom aksi tidak disaring/sorting
            ->rawColumns(['action', 'is_active'])//wajib render html
            ->make(true);
    }

    public function getDetailUser(Request $req){
        try {
            $item = User::find($req->user_id);
            if ($item) {
                return response()->json([
                    'status' => true,
                    'message' => 'succes',
                    'data' => $item,
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Failed: gagal Mendapatkan detail user',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed: Terjadi kesalahan Sistem, error: ' . $e->getMessage(),
            ]);
        }

    }

    public function profile() {
        $arrJk = [
            'Pria',
            'Wanita',
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
        $roles = Role::all();
        return view('pages.user.index', [
            'title' => 'Pengguna',
            'data' => $data,
            'roles' => $roles,
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
