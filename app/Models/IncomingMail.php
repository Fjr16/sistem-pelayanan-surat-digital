<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingMail extends Model
{
    protected $fillable = [
        'petugas_id',
        'penduduk_id',
        'mail_id',
        'file_path',
        'send_at',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'send_at' => 'datetime',
    ];

    public function penduduk(){
        return $this->belongsTo(User::class, 'penduduk_id', 'id');
    }
    public function petugas(){
        return $this->belongsTo(User::class, 'petugas_id', 'id');
    }
    public function incomingMailDetails(){
        return $this->hasMany(IncomingMailDetail::class);
    }
    public function mail(){
        return $this->belongsTo(Mail::class);
    }
}
