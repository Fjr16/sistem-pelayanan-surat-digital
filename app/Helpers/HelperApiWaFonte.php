<?php

namespace App\Helpers;

use App\Enums\ProcessStatus;
use App\Models\IncomingMail;

class HelperApiWaFonte
{
    protected $token;
    protected $url = 'https://api.fonnte.com/send';

    public function __construct() {
        $this->token = env('TOKEN_API_WA');
    }

    private function sendHttpRequest($request, $target) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => "Halo {$request['nama_pemohon']},\n\n"
                            . "Surat Anda: {$request['jenis_surat']}\n"
                            . "Nomor Surat: {$request['no_surat']}\n\n" 
                            ."Silahkan unduh surat disini: \n" 
                            . url('/unduh/surat/' . $request['encryptIncomingMailID'])
                            ."\n\nTerimakasih Sudah Menggunakan Layanan Kami 🙏",
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            return json_decode($error_msg, true);
        }
        return json_decode($response, true);
    }


    public function sendWaNotification(IncomingMail $item){
        try {
            if (!in_array($item->status, [ProcessStatus::finish->value, ProcessStatus::sent->value])) {
                return [
                    'status' => false,
                    'message' => 'Status surat tidak valid, surat bisa dikirim setelah disahkan',
                    'data' => null
                ];
            }
            if (!$item->file_path) {
                return [
                    'status' => false,
                    'message' => 'Surat Tidak ditemukan',
                    'data' => null
                ];
            }

            $target = $item->penduduk?->no_wa;
            if (!$target) {
                return [
                    'status' => false,
                    'message' => 'Nomor Whatsapp target tidak ditemukan',
                    'data' => null
                ];
            }

            $dataToSend = [
                'nama_pemohon' => $item->penduduk->name ?? '-',
                'jenis_surat' => $item->mail->name ?? '-',
                'no_surat' => $item->letter_number ?? '-',
                'encryptIncomingMailID' => encrypt($item->id),
            ];
            
            $res = $this->sendHttpRequest($dataToSend,$target);
            if (isset($res['status']) && $res['status']) {
                return [
                    'status' => true,
                    'message' => $res['detail'] ?? 'Pesan Terkirim',
                    'data' => $res,
                ];
            }else{
                return [
                    'status' => false,
                    'message' => $res['reason'] ?? 'Gagal Mengirim',
                    'data' => $res['requestid'] ?? null,
                ];
            }

        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
                'data' => null
            ];
        }
    }
}