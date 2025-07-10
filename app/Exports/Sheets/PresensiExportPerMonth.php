<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiExportPerMonth implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{

    private $data;
    private $rekap;
    private $month;
    private $year;

    public function __construct(Collection $data, array $rekap, int $year, int $month)
    {
        $this->data = $data;
        $this->rekap = $rekap;
        $this->month = $month;
        $this->year = $year;
    }

    public function array() : array {
        // Rekap section
        $rows[] = ['Rekap Kehadiran'];
        $rows[] = ['Nama', 'Hadir', 'Sakit', 'Izin', 'Alfa'];

        foreach ($this->rekap as $rekapRow) {
            $rows[] = [
                $rekapRow['nama'],
                $rekapRow['hadir'],
                $rekapRow['sakit'],
                $rekapRow['izin'],
                $rekapRow['alfa'],
            ];
        }

        // Spacer row
        $rows[] = array_fill(0, count($this->detailHeadings()), '');
        $rows[] = ['Detail Kehadiran'];
        $rows[] = $this->detailHeadings();

        foreach ($this->data as $item) {
            $rows[] = [
                $item['tanggal'] ?? '-',
                $item['jam_pelajaran'] ?? '-',
                $item['presensi'] ?? '-',
                $item['mata_pelajaran'] ?? '-',
                $item['guru'] ?? '-',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return now()->setYear($this->year)->setMonth($this->month)->translatedFormat('F Y');
    }
    private function detailHeadings(): array
    {
        return [
            'Tanggal',
            'Jam Pelajaran',
            'Presensi',
            'Mata Pelajaran',
            'Guru'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $rekapCount = count($this->rekap);
        $detailHeadingRow = 2 + $rekapCount + 3; // row 1 rekap kehadiran, row 2 heading tabel rekap kehadiran, row 3 data rekap kehadiran, row 4 row kosong, row 5 title detail kehadiran
        $detailTitleRow = $detailHeadingRow - 1;

        // Merge and center titles
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A{$detailTitleRow}:E{$detailTitleRow}");
        $sheet->getStyle("A{$detailTitleRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]], // Rekap Kehadiran
            2 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3']
            ]],
            $detailTitleRow => ['font' => ['bold' => true]],
            $detailHeadingRow => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3']
            ]],
        ];

    }
}
