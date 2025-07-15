<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = [
        'citizen_id',
        'mail_category_id',
        'file_path',
        'sent_at',
        'denied_note',
        'status',
        'input_user',
    ];

    protected function cast() {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function citizen() {
        return $this->belongsTo(Citizen::class);
    }
    public function mailCategory() {
        return $this->belongsTo(MailCategory::class);
    }
}
