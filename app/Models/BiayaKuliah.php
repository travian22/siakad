<?php

namespace App\Models;

use Database\Factories\BiayaKuliahFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mahasiswa_id', 'periode_akademik_id', 'nominal_tagihan', 'status_bayar'])]
class BiayaKuliah extends Model
{
    /** @use HasFactory<BiayaKuliahFactory> */
    use HasFactory;

    protected $table = 'biaya_kuliah';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_tagihan' => 'decimal:2',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function periodeAkademik(): BelongsTo
    {
        return $this->belongsTo(PeriodeAkademik::class);
    }
}
