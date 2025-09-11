<?php

namespace App\Http\Controllers;

use App\Models\IncomingMail;
use App\Models\Mail;
use Illuminate\Http\Request;
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
