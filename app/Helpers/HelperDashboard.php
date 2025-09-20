<?php

namespace App\Helpers;

use App\Enums\ProcessStatus;
use App\Enums\UserRole;
use App\Models\IncomingMail;
use App\Models\Mail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HelperDashboard
{
    public $model;
    public $allStatus;
    public $allRoles;
    public $allMailCategory;

    public function __construct() {
        Carbon::setLocale('id');
        $this->model = IncomingMail::query();
        $this->allStatus = ProcessStatus::cases();
        $this->allRoles = UserRole::cases();
        $this->allMailCategory = Mail::select('id', 'name')->get();
    }

    public function pendudukDashboard($idPenduduk){
        $dataStatusCounts = IncomingMail::select('status', DB::raw('count(*) as total'))
        ->where('penduduk_id', $idPenduduk)
        ->groupBy('status')
        ->pluck('total', 'status');

        $dataMailCounts = IncomingMail::select('mail_id', DB::raw('count(*) as total'))
        ->where('penduduk_id', $idPenduduk)
        ->groupBy('mail_id')
        ->pluck('total', 'mail_id');

        $data = [];
        foreach ($this->allStatus as $item) {
            $data['baseOnStatus'][] = [
                'key' => $item->name,
                'value' => $item->value,
                'label' => $item->label(),
                'color' => $item->color(),
                'jumlah' => $dataStatusCounts[$item->value] ?? 0,
            ];
        }
        foreach ($this->allMailCategory as $item) {
            $data['baseOnMail'][] = [
                'name' => $item->name,
                'jumlah' => $dataMailCounts[$item->id] ?? 0,
            ];
        }

        return $data;
    }
    public function dataPengajuanPerBulan($year){
        $data = [];
        for($i=1; $i<=12; $i++){
            $data[$i] = IncomingMail::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $i)
            ->count();
        }
        return $data;
    }
    public function baseOnStatusCurrentMonth(){
        $dataStatusCounts = IncomingMail::select('status', DB::raw('count(*) as total'))
        ->whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'))
        ->groupBy('status')
        ->pluck('total', 'status');

        $data = [];
        foreach ($this->allStatus as $item) {
            $data[] = [
                'key' => $item->name,
                'value' => $item->value,
                'label' => $item->label(),
                'color' => $item->color(),
                'jumlah' => $dataStatusCounts[$item->value] ?? 0,
            ];
        }
        return $data;
    }
    public function baseOnMailCurrentMonth(){
        $dataMailCounts = IncomingMail::select('mail_id', DB::raw('count(*) as total'))
        ->whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'))
        ->groupBy('mail_id')
        ->pluck('total', 'mail_id');

        $data = [];
        foreach ($this->allMailCategory as $item) {
            $data[] = [
                'name' => $item->name,
                'jumlah' => $dataMailCounts[$item->id] ?? 0,
            ];
        }
        return $data;
    }

    public function countUserByRole(){
         $dataRolesCount = User::select('role', DB::raw('count(*) as total'))
        ->groupBy('role')
        ->pluck('total', 'role');

        $data = [];
        foreach ($this->allRoles as $item) {
            $data[] = [
                'key' => $item->name,
                'value' => $item->value,
                'label' => $item->label(),
                'color' => $item->color(),
                'jumlah' => $dataRolesCount[$item->value] ?? 0,
            ];
        }
        return $data;
    }
}
