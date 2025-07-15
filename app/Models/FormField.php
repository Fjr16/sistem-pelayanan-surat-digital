<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'mail_category_id',
        'input_name',
        'input_label',
        'input_type',
        'is_required',
        'options',
    ];


    public function mailCategory() {
        return $this->belongsTo(MailCategory::class);
    }
}
