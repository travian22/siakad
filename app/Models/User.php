<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class);
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'dosen' => 'dosen.dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
            default => 'login',
        };
    }

    public function isProfileComplete(): bool
    {
        return match ($this->role) {
            'admin' => true,
            'dosen' => $this->loadMissing('dosen')->dosen?->isProfileComplete() ?? false,
            'mahasiswa' => $this->loadMissing('mahasiswa')->mahasiswa?->isProfileComplete() ?? false,
            default => false,
        };
    }

    public function displayName(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'dosen' => $this->loadMissing('dosen')->dosen?->nama_lengkap ?? $this->username,
            'mahasiswa' => $this->loadMissing('mahasiswa')->mahasiswa?->nama_lengkap ?? $this->username,
            default => $this->username,
        };
    }
}
