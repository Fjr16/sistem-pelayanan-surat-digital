<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailCategory extends Model
{
    protected $fillable = [
        'name',
        'template',
    ];

    public function mails(){
        return $this->hasMany(Mail::class);
    }
    public function fields() {
        return $this->hasMany(FormField::class);
    }
}
