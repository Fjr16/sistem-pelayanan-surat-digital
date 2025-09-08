<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'password',
        'email',
        'no_wa',
        'nik',
        'no_kk',
        'name',
        'gender',
        'tanggal_lhr',
        'tempat_lhr',
        'alamat_ktp',
        'alamat_dom',
        'agama',
        'status_kawin',
        'pekerjaan',
        'jabatan',
        'tanggal_masuk',
        'is_active',
        // 'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }
    public function IncomingMailsAsPenduduks() {
        return $this->hasMany(IncomingMail::class, 'penduduk_id', 'id');
    }
    public function IncomingMailsAsPetugas() {
        return $this->hasMany(IncomingMail::class, 'petugas_id', 'id');
    }
}
