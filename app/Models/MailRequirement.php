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
        'field_placeholder',
        'is_required',
        'options',
        'min',
        'max',
        'step',
        'inline',
    ];

    public function mail(){
        return $this->belongsTo(Mail::class);
    }
    // public function incomingMailDetails(){
    //     return $this->hasMany(IncomingMailDetail::class);
    // }
}
