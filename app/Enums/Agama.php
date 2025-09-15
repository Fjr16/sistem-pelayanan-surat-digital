<?php

namespace App\Enums;

enum Agama:string
{
    case islam = 'islam';
    case protestan = 'protestan';
    case katolik = 'katolik';
    case budha = 'budha';
    case hindu = 'hindu';
    case konghucu = 'konghucu';
    case other = 'lainnya';

    public function label(): string {
        return match ($this) {
            self::islam => 'Islam',
            self::protestan => 'Kristen Protestan',
            self::katolik => 'Kristen Katolik',
            self::budha => 'Budha',
            self::hindu => 'Hindu',
            self::konghucu => 'Konghucu',
            self::other => 'Lainnya',
        };
    }
}
