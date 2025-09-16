<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use App\Models\SignaturePosition;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
            $btnUpload = '<button type="button" class="btn btn-sm btn-primary" onclick="uploadSurat('. $row->id .')"><i class="bx bx-upload me-1" style="font-size:20px;"></i> Upload</button>';
            $btnLihat =  '<button type="button" class="btn btn-sm btn-info" onclick="openModal('. $row->id .')"><i class="bx bx-show me-1" style="font-size:20px;"></i> Lihat</button>';
            return '<div class="d-flex gap-1">
                '. $btnUpload . $btnLihat .'
            </div>';
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
            'incoming_mail_id' => 'required|exists:incoming_mails,id',
            'obj_ttd' => 'required|json',
            'canvas_width' => 'required',
            'canvas_height' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0, 150),
            ]);
        }
        try {
            $signature_position = json_decode($r->obj_ttd);
            DB::transaction(function () use ($r, $signature_position) {
                $item = IncomingMail::where('id', $r->incoming_mail_id)->lockForUpdate()->firstOrFail();
                $file_path = $r->file('surat_pdf')->store('upload/surat', 'public');
                $item->file_path = $file_path;
                $item->status = ProcessStatus::finish->value;
                $item->letter_number = $this->getNextLetterNumber($item->mail->id);
                $item->save();

                $signature = new SignaturePosition();
                $signature->incoming_mail_id = $item->id;
                $signature->page_number = $signature_position->page;
                $signature->signature_x = $signature_position->x;
                $signature->signature_y = $signature_position->y;
                $signature->signature_height = $signature_position->height;
                $signature->signature_width = $signature_position->width;
                $signature->canvas_width = $r->canvas_width;
                $signature->canvas_height = $r->canvas_height;
                $signature->save();
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

    private function getNextLetterNumber($idJenisSurat){
        $lastNumber = 0;
        $kodeUnit = 'WN';

        $item = IncomingMail::whereMonth('created_at', date('m'))
        ->latest('id')
        ->first();

        if ($item && $item->letter_number) {
            $lastNumber = (int) explode('/',$item->letter_number)[1];
        }

        return generateNomorSurat($lastNumber,$kodeUnit,$idJenisSurat);
    }
    // end upload surat

    // upload surat
    public function indexSent(){
        $countSurat = IncomingMail::whereIn('status', [
            ProcessStatus::finish->value,
            ProcessStatus::sent->value,
        ])->selectRaw('
            SUM(status = ?) as finishCount,
            SUM(status = ? AND (DATE(created_at) = CURDATE() OR DATE(updated_at) = CURDATE())) as sentCount
        ', [
            ProcessStatus::finish->value,
            ProcessStatus::sent->value
        ])->first();
        return view('pages.kirim-surat.index', [
            'title' => 'Pengesahan Surat Penduduk',
            'countSurat' => $countSurat
        ]);
    }
    public function showSent(){
        $stts = request()->get('status') ?? ProcessStatus::finish->value;
        $data = IncomingMail::query()
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
        if ($stts == ProcessStatus::sent->value) {
            $data->where(function($item){
                $item->whereDate('incoming_mails.created_at', date('Y-m-d'))
                ->orWhereDate('incoming_mails.updated_at', date('Y-m-d'));
            });
        }

        return DataTables::of($data)
        ->addColumn('action', function($row){
            if ($row->status == ProcessStatus::finish->value) {
                return '<button type="button" class="btn btn-sm btn-primary" onclick="signAndSend('. $row->id .')"><i class="bx bx-note me-1"></i> Sign & Send</button>';
            }elseif($row->status == ProcessStatus::sent->value){
                $btnSend = '<button type="button" class="btn btn-sm btn-primary" onclick="sendLetter('. $row->id .')"><i class="bx bx-send me-1"></i> Kirim Ulang</button>'; 
                $showBtn = '<button type="button" class="btn btn-sm btn-primary" onclick="showLetter(\''. $row->file_path .'\')"><i class="bx bx-show me-1"></i> Lihat Surat</button>';
                if ($row->send_at) {
                    return $showBtn;
                }else{
                    return '<div class="d-flex gap-1">
                    '. $btnSend . $showBtn .'
                    </div>';
                }
            }
        })
        ->editColumn('incoming_mails.created_at', function($row){
            return $row->created_at->format('d F Y, H:i');
        })
        ->editColumn('status', function($row){
            $badge = '<span class="badge bg-success">'.$row->status ?? "UNDEFINED".'</span>';
            if ($row->status == ProcessStatus::sent->value && !$row->send_at) {
                $badge = '<span class="badge bg-danger">Belum Terkirim</span>';
            }
            return $badge;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
    public function insertQrToPdf(Request $r){
        try {
            $incomingMail = IncomingMail::findOrFail($r->incoming_mail_id);
            // manual validation
            if (!$incomingMail->mail->name) {
                throw new \Exception("Jenis Surat Tidak diketahui");
            }
            if (!$incomingMail->letter_number) {
                throw new \Exception("Nomor Surat Tidak Ada");
            }
            if (!$incomingMail->created_at) {
                throw new \Exception("Waktu Pembuatan surat tidak ada");
            }
            if (!$incomingMail->penduduk) {
                throw new \Exception("Data Pemohon tidak ditemukan");
            }
            if (!$incomingMail->penduduk->name || !$incomingMail->penduduk->nik) {
                throw new \Exception("Data Pemohon tidak lengkap");
            }
            if (!$incomingMail->file_path) {
                throw new \Exception("Surat Tidak ditemukan");
            }

            $dataToBarcode = generateDigitalBarcode($incomingMail);
            $qrPath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            QrCode::format('png')->size(200)->generate($dataToBarcode, $qrPath);
            
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile(storage_path('app/public/' . $incomingMail->file_path));
            for($i = 1; $i<=$pageCount; $i++){
                $tpl = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);

                if ($i == $incomingMail->signaturePosition->page_number) {
                    $canvasWidth = $incomingMail->signaturePosition->canvas_width;
                    $canvasHeight = $incomingMail->signaturePosition->canvas_height;
                    
                    $pdfWidth = $size['width'];
                    $scaleX = $pdfWidth / $canvasWidth;
                    
                    $pdfHeight = $size['height'];
                    $scaleY = $pdfHeight / $canvasHeight;

                    $x = $incomingMail->signaturePosition->signature_x * $scaleX;
                    $y = $incomingMail->signaturePosition->signature_y * $scaleY;
                    $w = $incomingMail->signaturePosition->signature_width * $scaleX;
                    $h = $incomingMail->signaturePosition->signature_height * $scaleY;

                    $pdf->Image(realpath($qrPath), $x, $y, $w, $h);
                }
            }

            $tempPreview = tempnam(sys_get_temp_dir(),'pdf_preview_').'.pdf';
            $pdf->Output($tempPreview,'F');

            return response()->file($tempPreview, ['Content-Type'=>'application/pdf']);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ], 400);
        }
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
                // hapus file PDF lama jika ada
                if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }

                $file_path = $r->file('surat_pdf')->store('upload/surat', 'public');
                $item->file_path = $file_path;
                $item->status = ProcessStatus::sent->value;
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
