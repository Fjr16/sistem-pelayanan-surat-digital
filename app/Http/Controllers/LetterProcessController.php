<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;
use DateTime;
use Illuminate\Http\Request;
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
            SUM(status = ? AND DATE(created_at) = CURDATE()) as rejectedCount,
            SUM(status = ? AND DATE(created_at) = CURDATE()) as cancelCount
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
            return $item->whereDate('incoming_mails.created_at', date('Y-m-d'));
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
            $verifBtn = '<button type="button" class="btn btn-sm btn-primary" onclick="verifikasi('. $row->id .')">Verifikasi</button>';
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
            return '<button type="button" class="btn btn-sm btn-dark" onclick="uploadSurat('. $row->id .')">Upload</button>';
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
    public function storeUpload(){

    }
}
