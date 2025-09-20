<?php

namespace App\Enums;

enum UserRole: string
{
    case PENDUDUK = 'penduduk';
    case PETUGAS = 'petugas';
    case SEKRETARIS = 'sekretaris';
    case WALINAGARI = 'wali_nagari';


    public function label(): string{
        return match ($this) {
            self::PENDUDUK => 'Penduduk',
            self::PETUGAS => 'Petugas Kantor Wali Nagari',
            self::SEKRETARIS => 'Sekretaris Wali Nagari',
            self::WALINAGARI => 'Wali Nagari',
        };
    }
    public function color(): string{
        return match ($this) {
            self::PENDUDUK => 'bg-dark text-white',
            self::PETUGAS => 'bg-blue text-white',
            self::SEKRETARIS => 'bg-teal text-white',
            self::WALINAGARI => 'bg-primary text-white',
        };
    }
}
