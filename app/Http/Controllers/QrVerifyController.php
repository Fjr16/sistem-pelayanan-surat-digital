<?php

namespace App\Http\Controllers;

use App\Models\IncomingMail;

class QrVerifyController extends Controller
{
    public function verify(){
        $no_surat = request()->get('number');
        $signature = request()->get('sig');
        $surat = IncomingMail::where('letter_number', $no_surat)->first();
        $getMessage = $this->validation($no_surat, $signature, $surat);
        return view('pages.verifikasi-surat.show', compact('getMessage', 'surat'));
    }

    private function validation($no_surat, $signature, $surat){
        $secret_key = env('QR_SECRET_KEY');
        $expected = hash_hmac('sha256', $no_surat, $secret_key);
        $isEqual = hash_equals($signature, $expected);


        $msg = 'Surat Dinyatakan VALID (SAH)';
        if (!$surat) {
            $msg = 'Surat Tidak Terdaftar';
        }
        if (!$no_surat || !$signature || $isEqual == false) {
            $msg = 'Barcode Tidak Valid (Sudah Dipalsukan)';
        }

        return $msg;
    }
}
