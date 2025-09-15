<?php

namespace App\Enums;

enum MaritalStatus:string {
    case nikah = "menikah";
    case belum = "belum_menikah";
    case janda = "janda";
    case duda = "duda";
    case other = "lainnya";

    public function label(): string {
        return match($this){
            self::nikah => 'Menikah',
            self::belum => 'Belum Menikah',
            self::janda => 'Janda',
            self::duda => 'Duda',
            self::other => 'Lainnya',
        };
    }
}
