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
        $jenisSurat = $item->mail->name;
        $noSurat = $item->letter_number;
        $dibuatPada = $item->created_at->format('Y-m-d');
        $disahkanPada = $item->send_at ? Carbon\Carbon::parse($item->send_at)->format('Y-m-d') : date('Y-m-d');
        $nama_pemohon = $item->penduduk->name;
        $nik_pemohon = $item->penduduk->nik;

        return $dataBarcode = encrypt(json_encode([
            'jenis'   => $jenisSurat,
            'nomor'   => $noSurat,
            'buat'    => $dibuatPada,
            'sahkan'  => $disahkanPada,
            'nama'    => $nama_pemohon,
            'nik'     => $nik_pemohon,
        ]));
    }
}

if (!function_exists('sendNotifyToWa')) {
    function sendNotifyToWa(IncomingMail $item)
    {
        try {
            $jenisSurat = $item->mail->name;
            $noSurat = $item->letter_number;
            $nama_pemohon = $item->penduduk->name;
            $no_wa = $item->penduduk->no_wa;
    
            $message = "Halo ".$nama_pemohon.
                ", surat Anda : " . $jenisSurat . 
                ", dengan nomor : ". $noSurat .
                " sudah selesai. Silakan cek di link: ".route('proses/surat.unduh', $$item->id);
            
            return true;
        } catch (\Throwable $th) {
            return false;
            // log api
        }

    }
}
