<?php

namespace App\Enums;

enum ProcessStatus:string {
    case pending = "PENDING";
    case rejected = "REJECTED";
    case cancel = "CANCEL";
    case process = "ON PROCESS";
    case finish = "FINISH";
    case sent = "SENT";
    // case downloaded = "DOWNLOADED";

    public function label():string {
        return match($this){
            self::pending => 'Verifikasi',
            self::rejected => 'Ditolak',
            self::cancel => 'Dibatalkan',
            self::process => 'Sedang Dibuat',
            self::finish => 'Menunggu Pengesahan',
            self::sent => 'Selesai',
            // self::downloaded => 'Diunduh'
        };
    }
}
