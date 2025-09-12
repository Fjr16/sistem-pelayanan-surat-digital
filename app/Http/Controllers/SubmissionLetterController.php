<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use App\Models\IncomingMailDetail;
use App\Models\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $mail = Mail::find($r->mail_id);
        // $mail = Mail::find(50);
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

                if (!empty($row['values'])) {
                    $options = array_column($row['values'], 'value');
                }

                if($row['type'] == 'file'){
                    $max = 'max:2048';
                }


                $rules[$row['name']] = [
                    $row['required'] ? 'required' : 'nullable',
                    $type,
                    $row['min'] ?  : null,
                    $row['max'] ? 'max:' . $row['max'] : null,
                    $row['maxlength'] ? 'max:' . $row['max'] : null,
                    isset($options) ? 'in:' . implode($options) : null,
                    isset($max) ? $max : null,
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

        $incomingDetail = new IncomingMailDetail();
        $incomingDetail->incoming_mail_id = $incomingMail->id;
        // $incomingDetail->mail_requirement_id = $incomingMail->id;
        // $incomingDetail->value_basic = ;
        // $incomingDetail->value_text = ;
        // $incomingDetail->value_json = ;
        // $mailId = $request->mail_id;

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
