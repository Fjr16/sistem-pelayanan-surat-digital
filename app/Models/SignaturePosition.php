<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignaturePosition extends Model
{
    protected $fillable = [
        'incoming_mail_id',
        'page_number',
        'signature_x',
        'signature_y',
        'signature_height',
        'signature_width',
        'canvas_width',
        'canvas_height',
    ];

    public function incomingMail(){
        return $this->belongsTo(IncomingMail::class);
    }
}
