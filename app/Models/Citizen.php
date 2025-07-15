<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'nik',
        'tempat_lhr',
        'tanggal_lhr',
        'alamat',
        'hp',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
