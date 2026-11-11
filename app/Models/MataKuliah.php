<?php

namespace App\Models;

use Database\Factories\MataKuliahFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_mk', 'nama_mk', 'sks'])]
class MataKuliah extends Model
{
    /** @use HasFactory<MataKuliahFactory> */
    use HasFactory;

    protected $table = 'mata_kuliah';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sks' => 'integer',
        ];
    }

    public function kelasPerkuliahan(): HasMany
    {
        return $this->hasMany(KelasPerkuliahan::class);
    }
}
