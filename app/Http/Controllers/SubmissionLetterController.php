<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use App\Models\IncomingMailDetail;
use App\Models\Mail;
use DateTime;
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
        $countSurat = IncomingMail::where('penduduk_id', Auth::user()->id)
        ->selectRaw('
            SUM(status = ?) as pendingCount,
            SUM(status = ?) as processCount,
            SUM(status = ?) as rejectedCount,
            SUM(status = ?) as cancelCount,
            SUM(status = ?) as finishCount,
            SUM(status = ?) as sentCount
        ', [
            ProcessStatus::pending->value,
            ProcessStatus::process->value,
            ProcessStatus::rejected->value,
            ProcessStatus::cancel->value,
            ProcessStatus::finish->value,
            ProcessStatus::sent->value
        ])
        ->first();
        return view('pages.suratsaya.create', [
            'title' => 'Surat Saya',
            'countSurat' => $countSurat,
        ]);
    }
    // untuk halaman riwayat surat penduduk
    public function getSuratSaya(){
        $stts = request()->get('status') ?? ProcessStatus::pending;
        $data = IncomingMail::query()
        ->where('penduduk_id', Auth::user()->id)
        ->where('status', $stts)
        ->leftjoin('mails', 'incoming_mails.mail_id', '=', 'mails.id')
        ->leftjoin('users as pet', 'incoming_mails.petugas_id', '=', 'pet.id')
        ->leftjoin('users as pend', 'incoming_mails.penduduk_id', '=', 'pend.id')
        ->select(
            'incoming_mails.*',
            'mails.name as mail_name',
            'pet.name as petugas_name',
            'pend.name as penduduk_name',
        );

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $cancelBtn = '<button type="button" class="btn btn-sm btn-danger" onclick="cancelLetter('. $row->id .', \''. ProcessStatus::cancel->value .'\')">Batalkan</button>';
            $badgeReject = '<span class="badge bg-danger">DITOLAK</span>';
            $badgeProcess = '<span class="badge bg-info">SEDANG DIBUAT</span>';
            $badgeCancel = '<span class="badge bg-danger">DIBATALKAN</span>';
            $badgeFinish = '<span class="badge bg-primary">MENUNGGU PENGESAHAN</span>';
            $unduhBtn = '<button class="btn btn-sm btn-dark" onclick="unduhLetter('.$row->id.')">Unduh</button>';
            if ($row->status == ProcessStatus::pending->value) {
                return $cancelBtn;
            }elseif($row->status == ProcessStatus::process->value){
                return $badgeProcess;
            }elseif($row->status == ProcessStatus::rejected->value){
                return $badgeReject;
            }elseif($row->status == ProcessStatus::cancel->value){
                return $badgeCancel;
            }elseif($row->status == ProcessStatus::finish->value){
                return $badgeFinish;
            }elseif($row->status == ProcessStatus::sent->value){
                return $unduhBtn;
            }else{
                return '<span class="badge bg-warning">'.($row['status'] ?? 'UNDEFINED').'</span>';
            }
        })
        ->editColumn('incoming_mails.created_at', function($row){
            return $row->created_at->format('d F Y, H:i');
        })
        ->editColumn('incoming_mails.send_at', function($row){
            if ($row->send_at) {
                $sentAt = new DateTime($row->send_at);
                return $sentAt->format('d F Y, H:i');
            }
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function updateStatusPengajuan(Request $r){
        $incomingMailId = $r->incoming_mail_id;
        $stts = $r->status_update;
        $ket = $r->keterangan;
        try {
            DB::transaction(function() use ($incomingMailId, $stts, $ket){
                $item = IncomingMail::where('id', $incomingMailId)->lockForUpdate()->firstOrFail();
                $item->update([
                    'status' => $stts,
                    'keterangan' => $ket,
                ]);
            });
            return response()->json([
                'status' => true,
                'message' => "Berhasil membatalkan pengajuan",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }
    }
    // end surat saya
}
