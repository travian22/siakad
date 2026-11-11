<?php

namespace App\Models;

use Database\Factories\DosenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nip', 'nama_lengkap', 'jenis_kelamin', 'telepon', 'alamat', 'foto_profil'])]
class Dosen extends Model
{
    /** @use HasFactory<DosenFactory> */
    use HasFactory;

    protected $table = 'dosen';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelasPerkuliahan(): HasMany
    {
        return $this->hasMany(KelasPerkuliahan::class);
    }

    public function isProfileComplete(): bool
    {
        return filled($this->jenis_kelamin)
            && filled($this->telepon)
            && filled($this->alamat)
            && filled($this->foto_profil);
    }
}
