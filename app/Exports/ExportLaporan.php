<?php

namespace App\Exports;

use App\Enums\ProcessStatus;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportLaporan implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
            try {
                $stts = ProcessStatus::from($row->status)->label();
            } catch (\ValueError $e) {
                $stts = 'UNDEFINED';
            }
            return [
                'Nama Pemohon' => $row->nama_pemohon,
                'NIK Pemohon' => $row->nik_pemohon,
                'WA Pemohon'  => $row->wa_pemohon,
                'Jenis Surat' => $row->mail_name,
                'Nomor Surat' => $row->letter_number ?? '-',
                'Tanggal Pengajuan'=> $row->created_at,
                'Tanggal Pengesahan'=> $row->send_at ?? $row->updated_at,
                'Keterangan' => $row->keterangan ?? '-',
                'Status'      => $stts,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Pemohon',
            'NIK Pemohon',
            'WA Pemohon',
            'Jenis Surat',
            'Nomor Surat',
            'Tanggal Pengajuan',
            'Tanggal Pengesahan',
            'Keterangan',
            'Status',
        ];
    }
}
