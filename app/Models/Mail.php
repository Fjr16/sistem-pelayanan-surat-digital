<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function mailRequirements() {
        return $this->hasMany(MailRequirement::class);
    }
}
