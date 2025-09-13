<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LetterProcessController extends Controller
{
    // verifikasi surat
    public function indexVerifikasi(){
        $countSurat = IncomingMail::whereIn('status', [
            ProcessStatus::pending->value,
            ProcessStatus::rejected->value,
            ProcessStatus::cancel->value,
        ])->selectRaw('
            SUM(status = ?) as pendingCount,
            SUM(status = ? AND (DATE(created_at) = CURDATE() OR DATE(updated_at) = CURDATE())) as rejectedCount,
            SUM(status = ? AND (DATE(created_at) = CURDATE() OR DATE(updated_at) = CURDATE())) as cancelCount
        ', [
            ProcessStatus::pending->value,
            ProcessStatus::rejected->value,
            ProcessStatus::cancel->value
        ])->first();
        return view('pages.verifikasi-surat.index', [
            'title' => 'Verifikasi Surat Masuk',
            'countSurat' => $countSurat,
        ]);
    }

    public function getSuratVerifikasi(){
        $stts = request()->get('status') ?? ProcessStatus::pending;
        $data = IncomingMail::query()
        ->where('status', $stts)
        ->when($stts == ProcessStatus::cancel->value || $stts == ProcessStatus::rejected->value, function($item){
            return $item->where(function ($q){
                $q->whereDate('incoming_mails.created_at', date('Y-m-d'))
                ->orWhereDate('incoming_mails.updated_at', date('Y-m-d'));
            });
        })
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
            // $verifBtn = '<button type="button" class="btn btn-sm btn-primary" onclick="verifikasi('. $row->id .')">Verifikasi</button>';
            $verifBtn = '<button type="button" class="btn btn-sm btn-primary" onclick="openVerifikasiModal('. $row->id .')">Verifikasi</button>';
            $badgeReject = '<span class="badge bg-danger">DITOLAK</span>';
            $badgeCancel = '<span class="badge bg-danger">DIBATALKAN</span>';
            if ($row->status == ProcessStatus::pending->value) {
                return $verifBtn;
            }elseif($row->status == ProcessStatus::rejected->value){
                return $badgeReject;
            }elseif($row->status == ProcessStatus::cancel->value){
                return $badgeCancel;
            }else{
                return '<span class="badge bg-warning">'.($row['status'] ?? 'UNDEFINED').'</span>';
            }
        })
        ->editColumn('incoming_mails.created_at', function($row){
            return $row->created_at->format('d F Y, H:i');
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function getDetailPengajuan($incomingMailId) {
        $item = IncomingMail::query()
        ->with(['petugas', 'penduduk', 'incomingMailDetails'])
        ->findOrFail($incomingMailId);

        $requirements = $this->reformatMailRequirements($item);

        return response()->json([
            'id'             => $item->id,
            'mail_name'      => $item->mail->name ?? '-',
            'penduduk_name'  => $item->penduduk->name ?? '-',
            'petugas_name'   => $item->petugas->name ?? '-',
            'created_at'     => $item->created_at->format('d F Y, H:i'),
            'status'         => $item->status,
            'requirements'   => $requirements,
        ]);

    }

    public function updateStatusPengajuan(Request $r){
        $incomingMailId = $r->incoming_mail_id;
        $stts = null;
        if ($r->status_update == 'terima') {
            $stts = ProcessStatus::process->value;
        }else{
            $stts = ProcessStatus::rejected->value;
        }
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
                'message' => "Berhasil Verifikasi Pengajuan",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }
    }

    private function reformatMailRequirements($incomingMail){
        if (!$incomingMail || $incomingMail->incomingMailDetails->isEmpty()) {
            return null;
        }
        $arr = [];
        foreach ($incomingMail->incomingMailDetails as $key => $item) {
            $val = null; 
            if ($item->mailRequirement->field_type == 'checkbox-group') {
                $val = json_decode($item->value_json, true);
            }elseif($item->mailRequirement->field_type == 'textarea'){
                $val = $item->value_text;
            }else{
                $val = $item->value_basic;
            }
            $arr[] =  [
                'label' => $item->mailRequirement->field_label,
                'type' => $item->mailRequirement->field_type,
                'subtype' => $item->mailRequirement->field_type,
                'required' => $item->mailRequirement->is_required,
                'value' => $val,
                'is_filled' => !empty($val),
            ];
        }
        return $arr;
    }
    // end verifikasi surat

    // upload surat
    public function indexUpload(){
        return view('pages.upload-surat.index', [
            'title' => 'Upload Surat Penduduk',
        ]);
    }
    public function showUpload(){
        $data = IncomingMail::query()
        ->where('status', ProcessStatus::process->value)
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
            return '<button type="button" class="btn btn-sm btn-info" onclick="uploadSurat('. $row->id .')"><i class="bx bx-show me-1" style="font-size:20px;"></i> Lihat Detail</button>';
        })
        ->editColumn('incoming_mails.created_at', function($row){
            return $row->created_at->format('d F Y, H:i');
        })
        ->editColumn('status', function($row){
            return '<span class="badge bg-primary">'.$row->status ?? "UNDEFINED".'</span>';
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
    public function storeUpload(Request $r){
        $validators = Validator::make($r->all(), [
            'surat_pdf' => 'required|file|mimetypes:application/pdf',
            'incoming_mail_id' => 'required|exists:incoming_mails,id'
        ]);
        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0, 150),
            ]);
        }
        try {
            DB::transaction(function () use ($r) {
                $item = IncomingMail::where('id', $r->incoming_mail_id)->lockForUpdate()->firstOrFail();
                $file_path = $r->file('surat_pdf')->store('upload/surat', 'public');
                $item->file_path = $file_path;
                $item->status = ProcessStatus::finish->value;
                $item->save();
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Upload Surat, Menunggu Pengesahan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }
    // end upload surat

    // upload surat
    public function indexSent(){
        return view('pages.kirim-surat.index', [
            'title' => 'Pengesahan Surat Penduduk',
        ]);
    }
    public function showSent(){
        $data = IncomingMail::query()
        ->where('status', ProcessStatus::finish->value)
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
            return '<button type="button" class="btn btn-sm btn-primary" onclick="validation('. $row->id .')"><i class="bx bx-note me-1"></i> Sign & Send</button>';
        })
        ->editColumn('incoming_mails.created_at', function($row){
            return $row->created_at->format('d F Y, H:i');
        })
        ->editColumn('status', function($row){
            return '<span class="badge bg-success">'.$row->status ?? "UNDEFINED".'</span>';
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
    public function storeSent(Request $r){
        $validators = Validator::make($r->all(), [
            'surat_pdf' => 'required|file|mimetypes:application/pdf',
            'incoming_mail_id' => 'required|exists:incoming_mails,id'
        ]);
        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0, 150),
            ]);
        }
        try {
            DB::transaction(function () use ($r) {
                $item = IncomingMail::where('id', $r->incoming_mail_id)->lockForUpdate()->firstOrFail();
                $file_path = $r->file('surat_pdf')->store('upload/surat', 'public');
                $item->file_path = $file_path;
                $item->status = ProcessStatus::finish->value;
                $item->save();
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Upload Surat, Menunggu Pengesahan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }
    // end upload surat
}
