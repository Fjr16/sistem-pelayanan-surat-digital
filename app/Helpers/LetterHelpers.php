<?php

use App\Models\IncomingMail;

if (!function_exists('generateNomorSurat')) {
    function generateNomorSurat($lastNumber, $kodeUnit, $idJenisSurat)
    {
        $bulan = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $nextNumber = $lastNumber + 1;
        $month = $bulan[date('n')];
        $year = date('Y');

        // Contoh format: 001/046/UM/IX/2025
        return sprintf("%03d/%03d/%s/%s/%s", $idJenisSurat, $nextNumber, $kodeUnit, $month, $year);
    }
}

if (!function_exists('generateDigitalBarcode')) {
    function generateDigitalBarcode(IncomingMail $item)
    {
        $noSurat = $item->letter_number;
        $secret_key = env('QR_SECRET_KEY');
        $signature = hash_hmac('sha256',$noSurat,$secret_key);
        return url("letter/verify/code?number={$noSurat}&sig={$signature}");
    }
}
