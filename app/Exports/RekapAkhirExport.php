<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAkhirExport implements FromArray, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $rekapBaseStudent;
    protected $rekapBaseMapel;
    protected $rekapBaseJk;
    protected $rekapBaseTingkat;

    public function __construct(Collection $rekapBaseStudent, Collection $rekapBaseMapel, Collection $rekapBaseJk, Collection $rekapBaseTingkat) {
        $this->rekapBaseStudent = $rekapBaseStudent;
        $this->rekapBaseMapel = $rekapBaseMapel;
        $this->rekapBaseJk = $rekapBaseJk;
        $this->rekapBaseTingkat = $rekapBaseTingkat;
    }

    public function array() :array
    {
        $rows[] = ['Rekapitulasi Kehadiran Berdasarkan Siswa'];
        $rows[] = ['Nama Siswa', 'Hadir', 'Sakit', 'Izin', 'Alfa'];

        foreach ($this->rekapBaseStudent as $key => $rekapStudent) {
            $rows[] = [
                $rekapStudent['name'],
                $rekapStudent['hadir'],
                $rekapStudent['sakit'],
                $rekapStudent['izin'],
                $rekapStudent['alfa'],
            ];
        }

        $rows[] = array_fill(0, count($this->rekapBaseMapelHeadings()), '');

        $rows[] = ['Rekapitulasi Kehadiran Berdasarkan Mata Pelajaran'];
        $rows[] = $this->rekapBaseMapelHeadings();
        
        foreach ($this->rekapBaseMapel as $key => $rekapMapel) {
            $rows[] = [
                $rekapMapel['mapel'] ?? '',
                $rekapMapel['hadir'] ?? '',
                $rekapMapel['sakit'] ?? '',
                $rekapMapel['izin'] ?? '',
                $rekapMapel['alfa'] ?? '',
            ];
        }

        // berdsarkan jenis kelamin
        $rows[] = array_fill(0, count($this->rekapBaseJkHeadings()), '');

        $rows[] = ['Rekapitulasi Kehadiran Berdasarkan Jenis Kelamin'];
        $rows[] = $this->rekapBaseJkHeadings();
        
        foreach ($this->rekapBaseJk as $key => $rekapJk) {
            $rows[] = [
                $rekapJk['jenis_kelamin'] ?? '',
                $rekapJk['hadir'] ?? '',
                $rekapJk['sakit'] ?? '',
                $rekapJk['izin'] ?? '',
                $rekapJk['alfa'] ?? '',
            ];
        }
        // berdasarkan tingkat
        $rows[] = array_fill(0, count($this->rekapBaseTingkatHeadings()), '');

        $rows[] = ['Rekapitulasi Kehadiran Berdasarkan Tingkat Kelas'];
        $rows[] = $this->rekapBaseTingkatHeadings();
        
        foreach ($this->rekapBaseTingkat as $key => $rekapTingkat) {
            $rows[] = [
                $rekapTingkat['tingkatan'] ?? '',
                $rekapTingkat['hadir'] ?? '',
                $rekapTingkat['sakit'] ?? '',
                $rekapTingkat['izin'] ?? '',
                $rekapTingkat['alfa'] ?? '',
            ];
        }

        return $rows;
    }

    private function rekapBaseMapelHeadings() :array {
        return [
            'Mata Pelajaran',
            'Hadir',
            'Sakit',
            'Izin',
            'Alfa',
        ];
    }
    private function rekapBaseJkHeadings() :array {
        return [
            'Jenis Kelamin',
            'Hadir',
            'Sakit',
            'Izin',
            'Alfa',
        ];
    }
    private function rekapBaseTingkatHeadings() :array {
        return [
            'Tingkat',
            'Hadir',
            'Sakit',
            'Izin',
            'Alfa',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rekapCount = count($this->rekapBaseStudent);
        $rekapMapelHeadingRow = 2 + $rekapCount + 3; // row 1 rekap kehadiran, row 2 heading tabel rekap kehadiran, row 3 data rekap kehadiran, row 4 row kosong, row 5 title detail kehadiran
        $titleMapelRow = $rekapMapelHeadingRow - 1;
        
        $rekapMapelCount = count($this->rekapBaseMapel);
        $rekapJkHeadingRow = $rekapMapelHeadingRow + $rekapMapelCount + 3;
        $titleJkRow = $rekapJkHeadingRow - 1;

        $rekapJkCount = count($this->rekapBaseJk);
        $rekapTingkatHeadingRow = $rekapJkHeadingRow + $rekapJkCount + 3;
        $titleTingkatRow = $rekapTingkatHeadingRow - 1;

        // Merge and center titles
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A{$titleMapelRow}:E{$titleMapelRow}");
        $sheet->getStyle("A{$titleMapelRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->mergeCells("A{$titleJkRow}:E{$titleJkRow}");
        $sheet->getStyle("A{$titleJkRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->mergeCells("A{$titleTingkatRow}:E{$titleTingkatRow}");
        $sheet->getStyle("A{$titleTingkatRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        return [
            1 => ['font' => ['bold' => true]], // Rekap Kehadiran
            2 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3']
            ]],
            $titleMapelRow => ['font' => ['bold' => true]],
            $rekapMapelHeadingRow => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3']
            ]],
            $titleJkRow => ['font' => ['bold' => true]],
            $rekapJkHeadingRow => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3']
            ]],

            $titleTingkatRow => ['font' => ['bold' => true]],
            $rekapTingkatHeadingRow => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3']
            ]],
        ];

    }
}
