<?php

namespace App\Models;

use Database\Factories\MahasiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'program_studi_id', 'npm', 'nama_lengkap', 'angkatan', 'jenis_kelamin', 'alamat', 'foto_profil'])]
class Mahasiswa extends Model
{
    /** @use HasFactory<MahasiswaFactory> */
    use HasFactory;

    protected $table = 'mahasiswa';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function pesertaKelas(): HasMany
    {
        return $this->hasMany(PesertaKelas::class);
    }

    public function biayaKuliah(): HasMany
    {
        return $this->hasMany(BiayaKuliah::class);
    }
}
