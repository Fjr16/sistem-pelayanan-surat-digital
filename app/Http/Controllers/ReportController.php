<?php

namespace App\Http\Controllers;

use App\Enums\ProcessStatus;
use App\Exports\ExportLaporan;
use App\Models\IncomingMail;
use App\Models\Mail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function index(){
        $status = ProcessStatus::cases();
        $mails = Mail::all();
        return view('pages.report.index', [
            'title' => 'Master Export Laporan',
            'status' => $status,
            'mails' => $mails
        ]);
    }
    public function show() {
        $stts = request()->get('status_surat');
        $end = request()->get('end_date');
        $start = request()->get('start_date');
        $mail_id = request()->get('mail_id');
        $search = request()->get('search_filter');

        if (!$stts && !$end && !$start && !$mail_id) {
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $data = IncomingMail::query()
        ->select('incoming_mails.*', 'pend.name as nama_pemohon', 'pend.nik as nik_pemohon', 'pend.no_wa as wa_pemohon', 'mails.name as mail_name')
        ->leftjoin('users as pend', 'incoming_mails.penduduk_id','=', 'pend.id')
        ->leftjoin('mails', 'incoming_mails.mail_id','=', 'mails.id')
        ->with('penduduk')
        ->when($mail_id, fn($q, $mailId) => $q->where('mail_id', $mailId))
        ->when($stts, fn($q, $status) => $q->where('status', $status))
        ->when($start,
            fn($q, $startDate) => $q->whereDate('incoming_mails.created_at', '>=', $startDate))
        ->when($end,
            fn($q, $endDate) => $q->whereDate('incoming_mails.created_at', '<=', $endDate),
            fn($q) => $q->whereDate('incoming_mails.created_at', '<=', date('Y-m-d'))
        )
        ->when($search, function($q, $keyword) {
            $q->whereHas('penduduk', function($query) use ($keyword){
                $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('nik', 'like', "%{$keyword}%")
                ->orWhere('no_wa', 'like', "%{$keyword}%");
            });
        });

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('aksi', function($row){
            return '<button class="btn btn-sm btn-primary" onclick="showSurat('.$row->id.')"><i class="bx bx-show me-1" style="font-size:18px;"></i> Lihat</button>';
        })
        ->editColumn('created_at', function($row){
            return $row->created_at ? Carbon::parse($row->created_at)->format('d F Y') : '-';
        })
        ->editColumn('updated_at', function($row){
            return $row->updated_at ? Carbon::parse($row->updated_at)->format('d F Y') : '-';
        })
        ->editColumn('status', function($row){
            if($row->status){
                try {
                    return ProcessStatus::from($row->status)->label();
                } catch (\ValueError $e) {
                    return 'UNDEFINED';
                }
            }
            return 'UNDEFINED';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function exportExcel(){
        $stts = request()->get('status_surat');
        $end = request()->get('end_date');
        $start = request()->get('start_date');
        $mail_id = request()->get('mail_id');
        $search = request()->get('search_filter');

        $query = IncomingMail::query()
        ->select('incoming_mails.*', 'pend.name as nama_pemohon', 'pend.nik as nik_pemohon', 'pend.no_wa as wa_pemohon', 'mails.name as mail_name')
        ->leftjoin('users as pend', 'incoming_mails.penduduk_id','=', 'pend.id')
        ->leftjoin('mails', 'incoming_mails.mail_id','=', 'mails.id')
        ->with('penduduk')
        ->when($mail_id, fn($q, $mailId) => $q->where('mail_id', $mailId))
        ->when($stts, fn($q, $status) => $q->where('status', $status))
        ->when($start,
            fn($q, $startDate) => $q->whereDate('incoming_mails.created_at', '>=', $startDate))
        ->when($end,
            fn($q, $endDate) => $q->whereDate('incoming_mails.created_at', '<=', $endDate),
            fn($q) => $q->whereDate('incoming_mails.created_at', '<=', date('Y-m-d'))
        )
        ->when($search, function($q, $keyword) {
            $q->whereHas('penduduk', function($query) use ($keyword){
                $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('nik', 'like', "%{$keyword}%")
                ->orWhere('no_wa', 'like', "%{$keyword}%");
            });
        });

        return Excel::download(new ExportLaporan($query->get()), 'laporan.xlsx');
    }
}
