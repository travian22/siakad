<?php

namespace App\Models;

use Database\Factories\KelasPerkuliahanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['periode_akademik_id', 'mata_kuliah_id', 'dosen_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan'])]
class KelasPerkuliahan extends Model
{
    /** @use HasFactory<KelasPerkuliahanFactory> */
    use HasFactory;

    public const array DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    protected $table = 'kelas_perkuliahan';

    public function daySortOrder(): int
    {
        $index = array_search($this->hari, self::DAYS, true);

        return $index === false ? 99 : $index + 1;
    }

    public function periodeAkademik(): BelongsTo
    {
        return $this->belongsTo(PeriodeAkademik::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pesertaKelas(): HasMany
    {
        return $this->hasMany(PesertaKelas::class);
    }
}
