<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingMailDetail extends Model
{
    protected $fillable = [
        'incoming_mail_id',
        'mail_requirement_id',
        'value_basic',
        'value_text',
        'value_json',
    ];

    public function incomingMail(){
        return $this->belongsTo(IncomingMail::class);
    }
    public function mailRequirement(){
        return $this->belongsTo(MailRequirement::class);
    }
}
