<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use App\Models\IncomingMailDetail;
use App\Models\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SubmissionLetterController extends Controller
{
    public function create(){
        $data = Mail::all();
        return view('pages.letterIn.create', [
            'title' => 'Pengajuan Surat',
            'data' => $data
        ]);
    }

    private function reformatMailRequirements($mail){
        if (!$mail || $mail->mailRequirements->isEmpty()) {
            return null;
        }
        $result = $mail->mailRequirements->map(function($row){
            return [
                'id' => $row->id,
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

    public function getSchema($mail_id){
        try {
            $item = Mail::findOrFail($mail_id);
            $detail = $this->reformatMailRequirements($item);

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => [
                    'mail' => $item,
                    'mailRequirements' => $detail,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }

    public function store(Request $r){
        if (!$r->mail_id) {
            return response()->json([
                'status' => false,
                'message' => 'Surat Tidak Ditemukan',
            ]);
        }
        try {
            DB::beginTransaction();
            $mail = Mail::findOrFail($r->mail_id);
            $schema = $this->reformatMailRequirements($mail);
    
            $rules = [];
            if ($schema) {
                foreach ($schema as $key => $row) {
                    if (in_array($row['type'],['text', 'password', 'color', 'tel', 'url', 'textarea', 'radio-group', 'select'])) {
                        $type = "string";
                    }elseif($row['type'] == 'email'){
                        $type = "email";
                    }elseif(in_array($row['type'],['number', 'range'])){
                        $type = "number";
                    }elseif($row['type'] == 'datetime-local'){
                        $type = "datetime";
                    }elseif($row['type'] == 'checkbox-group'){
                        $type = "array";
                    }else{
                        $type = $row['type'];
                    }
    
    
                    $rules[$row['name']] = [
                        $row['required'] ? 'required' : 'nullable',
                        $type,
                        $row['min'] ?  : null,
                        $row['max'] ? 'max:' . $row['max'] : ($row['type'] == 'file' ? 'max:2048' : null),
                        $row['maxlength'] ? 'max:' . $row['max'] : null,
                        $row['values'] ? 'in:' . implode(array_column($row['values'], 'value')) : null,
                    ];
                }
            }
            $validators = Validator::make($r->all(), $rules);
            if ($validators->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => substr($validators->errors()->first(), 0, 150)
                ]);
            }
    
            $incomingMail = new IncomingMail();
            $incomingMail->penduduk_id = Auth::user()->id;
            $incomingMail->mail_id = $mail->id;
            $incomingMail->status = ProcessStatus::pending;
            $incomingMail->save();
    
            if ($schema) {
                foreach ($schema as $key => $row) {
                    $incomingDetail = new IncomingMailDetail();
                    $incomingDetail->incoming_mail_id = $incomingMail->id;
                    $incomingDetail->mail_requirement_id = $row['id'];
                    if ($row['type'] == 'checkbox-group') {
                        $incomingDetail->value_basic = null;
                        $incomingDetail->value_text = null;
                        $incomingDetail->value_json = $r->input($row['name']) ? json_encode($r->input($row['name'])) : null;
                    }elseif($row['type'] == 'textarea'){
                        $incomingDetail->value_basic = null;
                        $incomingDetail->value_text = $r->input($row['name']);
                        $incomingDetail->value_json = null;
                    }elseif($row['type'] == 'file'){
                        if ($r->file($row['name'])) {
                            $file_path = $r->file($row['name'])->store('upload/persayaratan', 'public');
                            $incomingDetail->value_basic = $file_path;
                        }else{
                            $incomingDetail->value_basic = null;
                        }
                        $incomingDetail->value_text = null;
                        $incomingDetail->value_json = null;
                    }else{
                        $incomingDetail->value_basic = $r->file($row['name']);
                        $incomingDetail->value_text = null;
                        $incomingDetail->value_json = null;
                    }
                    $incomingDetail->save();
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => "Pengajuan Surat Berhasil, menunggu verifikasi petugas"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }
    }

    // surat saya
    public function index(){
        return view('pages.suratsaya.create', [
            'title' => 'Surat Saya',
        ]);
    }
    // public function show(){
    //     $stts = request()->get('status');
    //     $data = IncomingMail::query()
    //     ->when($stts, function($row) use ($stts){
    //         return $row->where('status', $stts);
    //     });

    //     return DataTables::of($data)
    //     ->addColumn('action', function($row){

    //     })
    //     ->rawColumns(['action'])
    //     ->make(true);
    // }
    // end surat saya
}
