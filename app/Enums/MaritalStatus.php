<?php

namespace App\Enums;

enum MaritalStatus:string {
    case nikah = "Sudah Menikah";
    case belum_menikah = "Belum Menikah";
    case janda = "Janda";
    case duda = "Duda";
    case other = "Lainnya";
}