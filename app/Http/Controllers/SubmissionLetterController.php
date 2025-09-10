<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use Illuminate\Http\Request;

class SubmissionLetterController extends Controller
{
    public function create(){
        $data = Mail::all();
        return view('pages.letterIn.create', [
            'title' => 'Pengajuan Surat',
            'data' => $data
        ]);
    }
}
