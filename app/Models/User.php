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
        'roles',
        'email_verified_at',
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
            'roles' => 'array',
        ];
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function getRolesList(): array
    {
        $roles = $this->roles ?? [];
        if (empty($roles)) {
            $roles = $this->role ? [$this->role] : [];
            if ($this->dosen) {
                $roles[] = 'dosen';
                $jabatan = strtolower($this->dosen->jabatan ?? '');
                if ($jabatan === 'kaprodi') {
                    $roles[] = 'kaprodi';
                } elseif ($jabatan === 'direktur') {
                    $roles[] = 'direktur';
                }
            }
        } elseif ($this->dosen && ! in_array('dosen', $roles)) {
            $roles[] = 'dosen';
        }

        return array_values(array_unique(array_map('strtolower', $roles)));
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtolower($role), $this->getRolesList());
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDirektur(): bool
    {
        if ($this->hasRole('direktur')) {
            return true;
        }

        foreach ($this->getRolesList() as $r) {
            if (str_starts_with($r, 'direktur')) {
                return true;
            }
        }

        if ($this->isDosen()) {
            $jabatan = strtolower($this->dosen?->jabatan ?? '');
            if (str_contains($jabatan, 'direktur')) {
                return true;
            }
        }

        return false;
    }

    public function isDosen(): bool
    {
        return $this->hasRole('dosen') || $this->dosen !== null;
    }

    public function isKaprodi(): bool
    {
        if ($this->hasRole('kaprodi')) {
            return true;
        }

        foreach ($this->getRolesList() as $r) {
            if (str_starts_with($r, 'kaprodi')) {
                return true;
            }
        }

        if ($this->isDosen()) {
            $jabatan = strtolower($this->dosen?->jabatan ?? '');
            if (str_contains($jabatan, 'kaprodi')) {
                return true;
            }
        }

        return false;
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole('mahasiswa');
    }
}
