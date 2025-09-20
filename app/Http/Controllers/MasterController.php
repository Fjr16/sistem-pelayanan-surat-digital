<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Helpers\HelperDashboard;
use App\Models\IncomingMail;
use App\Models\Mail;
use App\Models\MailRequirement;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterController extends Controller
{
    // start dashboard
    public function dashboard() {
        $year = request()->get('year') ?? now()->year;

        $helperDashboards = new HelperDashboard();
        $role = Auth::user()->role;
        $data = [];
        $query = IncomingMail::query();
        if($role == UserRole::PENDUDUK->value){
            $data = $helperDashboards->pendudukDashboard(Auth::user()->id);
            $query->where('penduduk_id', Auth::user()->id);
        }elseif($role == UserRole::PETUGAS->value){
            $data['title'] = 'Grafik Data Pengajuan Surat';
            $data['byMonth'] = $helperDashboards->dataPengajuanPerBulan($year);
            $data['baseOnStatus'] = $helperDashboards->baseOnStatusCurrentMonth();
        }elseif($role == UserRole::SEKRETARIS->value){
            $data['title'] = 'Grafik Data Pengajuan Surat';
            $data['byMonth'] = $helperDashboards->dataPengajuanPerBulan($year);
            $data['baseOnStatus'] = $helperDashboards->baseOnStatusCurrentMonth();
            $data['baseOnMail'] = $helperDashboards->baseOnMailCurrentMonth();
            $data['baseOnRoles'] = $helperDashboards->countUserByRole();
        }elseif($role == UserRole::WALINAGARI->value){
            $data['title'] = 'Grafik Data Pengajuan Surat';
            $data['byMonth'] = $helperDashboards->dataPengajuanPerBulan($year);
            $data['baseOnStatus'] = $helperDashboards->baseOnStatusCurrentMonth();
            $data['baseOnMail'] = $helperDashboards->baseOnMailCurrentMonth();
        }
        $totalPengajuan = $query->count();
        return view('pages.dashboard', [
            'year' => $year,
            'data' => $data,
            'totalPengajuan' => $totalPengajuan
        ]);
    }
    // end dashboard

    private function formatToFormBuilder($item){
        if ($item->mailRequirements->isEmpty()) {
            return null;
        }
        $result = $item->mailRequirements->map(function($row){
            return [
                'label' => $row->field_label,
                'name' => $row->field_name,
                'type' => $row->field_type,
                'subtype' => $row->field_type,
                'placeholder' => $row->field_placeholder,
                'required' => $row->is_required,
                'values' => json_decode($row->options),
                'min' => $row->min,
                'max' => $row->max,
                'maxlength' => $row->max,
                'step' => $row->step,
                'inline' => $row->inline,
                'className' => ($row->field_type == 'radio-group' || $row->field_type == 'checkbox-group') ? null : 'form-control',
            ];
        });

        return $result;
    }

    /* controller master data surat */
    public function indexMail(){
        $data = Mail::all();
        return view('pages.mail.index', [
            'title' => 'Data Jenis Surat',
            'data' => $data,
        ]);
    }
    public function createMail(){
        $idToUpdate = request()->query('edit');
        $item = null;
        $formBuilderData = null;
        if ($idToUpdate) {
            try {
                $mail_id = decrypt($idToUpdate);
                $item = Mail::find($mail_id);

                if (!$item) {
                    return back()->with('error', 'Surat Tidak Ditemukan');
                }
                $formBuilderData = $this->formatToFormBuilder($item);

            } catch (DecryptException $de) {
                return back()->with('error', substr($de->getMessage(), 0, 100));
            }
        }
        return view('pages.mail.create', [
            'title' => 'Tambah Jenis Surat',
            'item' => $item,
            'formBuilderData' => $formBuilderData,
        ]);
    }
    public function storeMail(Request $request) {
        try {
            $validators = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'nullable|string',
                'schema' => 'nullable|json',
                'mail_id' => 'nullable|exists:mails,id',
                'is_active' => 'required'
            ]);
            if ($validators->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => substr($validators->errors()->first(), 0, 150),
                ]);
            }

            DB::beginTransaction();

            $decodeJson = json_decode($request->schema);
            $mailId = $request->mail_id ?? null;

            $mail = $mailId ? Mail::findOrFail($mailId) : new Mail();

            $mail->name = $request->name;
            $mail->description = $request->description;
            $mail->is_active = $request->is_active;
            if($mail->save()){
                if ($mailId) {
                    $mail->mailRequirements()->delete();
                }
                foreach ($decodeJson as $item) {
                    $mailRequirement = new MailRequirement();
                    $mailRequirement->mail_id = $mail->id;
                    $mailRequirement->field_label = $item->label;
                    $mailRequirement->field_name = $item->name;
                    $mailRequirement->field_type = isset($item->subtype) ? $item->subtype : $item->type;
                    $mailRequirement->field_placeholder = isset($item->placeholder) ? $item->placeholder : null;
                    $mailRequirement->is_required = $item->required;
                    $mailRequirement->options = isset($item->values) ? json_encode($item->values) : null;
                    $mailRequirement->min = isset($item->min) ? $item->min : null;
                    $mailRequirement->max = isset($item->max) ? $item->max : (isset($item->maxlength) ? $item->maxlength : null);
                    $mailRequirement->step = isset($item->step) ? $item->step : null;
                    $mailRequirement->inline = isset($item->inline) ? $item->inline : false;
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

    public function destroyMail($id) {
        try {
            $item = Mail::findOrFail(decrypt($id));
            $item->mailRequirements()->delete();
            $item->delete();
            return back()->with('success', 'Data Berhasil Dihapus');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi Kesalahan : ' . substr($e->getMessage(), 0, 150));
        }
    }
    /* End controller master data surat */
}
