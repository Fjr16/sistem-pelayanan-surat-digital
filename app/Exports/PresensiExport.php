<?php

namespace App\Exports;

use App\Exports\Sheets\PresensiExportPerMonth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PresensiExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $allData;
    protected $year;
    protected $monthStart;
    protected $monthEnd;
    protected $studentName;

    public function __construct(Collection $allData, int $year, int $monthStart, int $monthEnd, string $studentName)
    {
        $this->allData = $allData;
        $this->monthStart = $monthStart;
        $this->monthEnd = $monthEnd;
        $this->year = $year;
        $this->studentName = $studentName;
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($month = $this->monthStart; $month <= $this->monthEnd; $month++) {
            $monthlyData = $this->allData->filter(function ($item) use ($month) {
                return \Carbon\Carbon::parse($item['tanggal'])->month === $month;
            });

              $monthlyRekap = [
                    [
                        'nama' => $this->studentName ?? '-',
                        'hadir' => $monthlyData->where('presensi', 'hadir')->count(),
                        'sakit' => $monthlyData->where('presensi', 'sakit')->count(),
                        'izin'  => $monthlyData->where('presensi', 'izin')->count(),
                        'alfa'  => $monthlyData->where('presensi', 'alfa')->count(),
                    ]
                ];


            $sheets[] = new PresensiExportPerMonth($monthlyData, $monthlyRekap, $this->year, $month);
        }

        return $sheets;
    }
}
