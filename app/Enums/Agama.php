<?php

namespace App\Enums;

enum Agama:string 
{
    case islam = 'Islam';
    case protestan = 'Kristen Protestan'; 
    case katolik = 'Kristen Katolik'; 
    case budha = 'Budha'; 
    case hindu = 'Hindu'; 
    case konghucu = 'Konghucu'; 
    case other = 'Lainnya'; 
}