<?php

namespace App\Http\Controllers;

use App\Enums\Agama;
use App\Enums\MaritalStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function getData(){
        $role = request()->get('role_name');
        $data = User::query()
        ->when($role, function($q) use ($role){
            return $q->where('role', $role);
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
                $bg = $item->is_active ? 'success' : 'danger';
                $icon = $item->is_active ? 'check' : 'x';
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
                'message' => substr($e->getMessage(),0,150),
            ]);
        }

    }

    // profile
    public function profile() {
        $arrJk = [
            'Pria',
            'Wanita',
        ];
        $agama = Agama::cases();
        $maritalStts = MaritalStatus::cases();

        return view('pages.user.profile.index', [
            'arrJk' => $arrJk,
            'agama' => $agama,
            'maritalStts' => $maritalStts,
        ]);
    }
    // endProfile

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $arrJk = [
            'Pria',
            'Wanita',
        ];
        $agama = Agama::cases();
        $maritalStts = MaritalStatus::cases();
        $data = User::all();
        $roles = UserRole::cases();
        return view('pages.user.index', [
            'title' => 'Pengguna',
            'data' => $data,
            'roles' => $roles,
            'arrJk' => $arrJk,
            'agama' => $agama,
            'maritalStts' => $maritalStts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!$request['role']) {
                $request['role'] = UserRole::PENDUDUK->value;
            }
            DB::beginTransaction();
            $data = $request->validate([
                'name' => 'required|string',
                'username' => 'required|unique:users,username',
                'email' => 'required|unique:users,email',
                'role' => ['required', Rule::enum(UserRole::class)],
                'password' => 'required',
                'nik' => 'required|unique:users,nik',
                'no_kk' => 'required',
                'no_wa' => 'required|unique:users,no_wa',
                'name' => 'required|string',
                'gender' => 'required|in:Pria,Wanita',
                'tempat_lhr' => 'required|string',
                'tanggal_lhr' => 'required|date|before:today',
                'agama' => ['required', Rule::enum(Agama::class)],
                'status_kawin' => ['required', Rule::enum(MaritalStatus::class)],
                'pekerjaan' => 'nullable|string',
                'jabatan' => 'nullable|string',
                'tanggal_masuk' => 'nullable|date',
                'alamat_ktp' => 'nullable|string',
                'alamat_dom' => 'nullable|string',
            ]);

            if(!$request->is_active) $data['is_active'] = false;
            $data['password'] = Hash::make($request->password);

            User::create($data);

            DB::commit();
            return back()->with('success', 'Berhasil Disimpan');
        } catch (Throwable $e) {
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
                'role' => ['required', Rule::enum(UserRole::class)],
                'nik' => 'required|unique:users,nik,' . $item->id,
                'no_kk' => 'required',
                'no_wa' => 'required|unique:users,no_wa,' . $item->id,
                'name' => 'required|string',
                'gender' => 'required|in:Pria,Wanita',
                'tempat_lhr' => 'required|string',
                'tanggal_lhr' => 'required|date|before:today',
                'agama' => ['required', Rule::enum(Agama::class)],
                'status_kawin' => ['required', Rule::enum(MaritalStatus::class)],
                'pekerjaan' => 'nullable|string',
                'jabatan' => 'nullable|string',
                'tanggal_masuk' => 'nullable|date',
                'alamat_ktp' => 'nullable|string',
                'alamat_dom' => 'nullable|string',
            ]);

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }
            if ($request->is_active) {
                $data['is_active'] = true;
            }else{
                $data['is_active'] = false;
            }

            $item->update($data);

            DB::commit();
            return back()->with('success', 'Berhasil Diperbarui');
        } catch (Throwable $e) {
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
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
        }
    }

    // public function restore(Request $request, $id) {
    //     try {
    //         DB::beginTransaction();
    //         $item = User::withTrashed()->findOrFail(decrypt($id));
    //         $item->restore();

    //         DB::commit();
    //         return back()->with('success', 'Berhasil Dipulihkan');
    //     } catch (Throwable $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
    //     }
    // }

    // public function forceDelete($id) {
    //     try {
    //         DB::beginTransaction();
    //         $item = User::withTrashed()->findOrFail(decrypt($id));
    //         $item->forceDelete();

    //         DB::commit();
    //         return back()->with('success', 'Berhasil Dihapus Permanen');
    //     } catch (Throwable $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Terjadi kesalahan: '. $e->getMessage());
    //     }
    // }
}
