<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
    public function dosen(){
        return $this->hasOne(Dosen::class);
    }
    public function mahasiswa(){
        return $this->hasOne(Mahasiswa::class);
    }
    public function isAdmin() {
        return $this->role === 'admin';
    }
    public function isDirektur() {
        return $this->role === 'direktur';
    }
    public function isDosen() {
        return $this->role === 'dosen';
    }
    public function isKaprodi() {
        return $this->role === 'dosen' 
            && strtolower($this->dosen?->jabatan ?? '') === 'kaprodi';
    }
    public function isMahasiswa() {
        return $this->role === 'mahasiswa';
    }
}
