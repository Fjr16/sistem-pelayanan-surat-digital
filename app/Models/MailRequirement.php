<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailRequirement extends Model
{
    protected $fillable = [
        'mail_id',
        'field_label',
        'field_name',
        'field_type',
        'is_required',
        'options',
        'urutan',
    ];

    public function mail(){
        return $this->belongsTo(Mail::class);
    }
    // public function incomingMailDetails(){
    //     return $this->hasMany(IncomingMailDetail::class);
    // }
}
