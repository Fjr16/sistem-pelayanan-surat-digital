<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Models\MailRequirement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    /* controller master data surat */
    public function indexMail(){
        $data = Mail::all();
        return view('pages.mail.index', [
            'title' => 'Jenis Surat',
            'data' => $data,
        ]);
    }
    public function storeMail(Request $request) {
        try {
            $validators = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'nullable|string',
                'schema' => 'nullable|json'
            ]);
            if ($validators->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => substr($validators->errors()->first(), 0, 150),
                ]);
            }
            // return json_decode($request->schema);

            DB::beginTransaction();

            $decodeJson = json_decode($request->schema);

            $mail = new Mail();

            $mail->name = $request->name;
            $mail->description = $request->description;
            $mail->is_active = true;
            if($mail->save()){
                foreach ($decodeJson as $key => $item) {
                    $mailRequirement = new MailRequirement();
                    $mailRequirement->mail_id = $mail->id;
                    $mailRequirement->field_label = $item->label;
                    $mailRequirement->field_name = $item->name;
                    $mailRequirement->field_type = $item->type;
                    $mailRequirement->is_required = $item->required;
                    $mailRequirement->options = isset($item->values) ? json_encode($item->values) : null;
                    $mailRequirement->save();
                }
            }

            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Process successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }

    // public function updatePenduduk(Request $request, $id) {
    //     DB::beginTransaction();
    //     try {
    //         $item = Citizen::findOrFail($id);
    //         $data = $request->validate([
    //             'name' => [
    //                 'required',
    //                 'string',
    //                 Rule::unique('subjects')->ignore($id)->where(function($query) use ($request) {
    //                     return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
    //                 }),
    //             ],
    //             'code' => [
    //                 'required',
    //                 'string',
    //                 Rule::unique('subjects')->ignore($id)->where(function($query) use ($request) {
    //                     return $query->whereRaw('LOWER(code) = ?', [strtolower($request->name)]);
    //                 }),
    //             ],
    //         ], [
    //             'name.required' => 'Nama mapel harus diisi',
    //             'name.string' => 'Format Data Tidak Valid',
    //             'name.unique' => 'nama mapel Telah digunakan',
    //             'code.required' => 'Kode mapel harus diisi',
    //             'code.string' => 'format data Tidak Valid',
    //             'code.unique' => 'kode mapel Telah digunakan',
    //         ]);
            
    //         $item->update($data);
            
    //         DB::commit();
    //         return back()->with('success', 'Data Berhasil Disimpan');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Kesalahan: ' . $e->getMessage());
    //     } catch (ModelNotFoundException $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Kesalahan: ' . $e->getMessage());
    //     } catch (ValidationException $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Kesalahan: ' . $e->getMessage());
    //     }
    // }
    // public function destroyPenduduk($id) {
    //     try {
    //         $item = Citizen::findOrFail(decrypt($id));
    //         $item->delete();
    //         return back()->with('success', 'Data Berhasil Dihapus');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Kesalahan: ' . $e->getMessage());
    //     } catch (ModelNotFoundException $e) {
    //         return back()->with('error', 'Kesalahan: ' . $e->getMessage());
    //     }
    // }
    /* End controller master data surat */
}
