<?php

namespace App\Models;

use Database\Factories\PeriodeAkademikFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_periode', 'is_active'])]
class PeriodeAkademik extends Model
{
    /** @use HasFactory<PeriodeAkademikFactory> */
    use HasFactory;

    protected $table = 'periode_akademik';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function kelasPerkuliahan(): HasMany
    {
        return $this->hasMany(KelasPerkuliahan::class);
    }

    public function biayaKuliah(): HasMany
    {
        return $this->hasMany(BiayaKuliah::class);
    }
}
