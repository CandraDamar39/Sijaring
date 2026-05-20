<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produk extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'kode_produk',
        'nama',
        'spec',
        'deskripsi',
        'kategori',
        'harga',
        'stok',
        'is_aktif',
        'foto',
        'tone',
    ];

    /**
     * Native type casting.
     */
    protected $casts = [
        'harga'    => 'decimal:2',
        'stok'     => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Many-to-many relation: produk ↔ kategori (via pivot tabel produk_kategori).
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(
            Kategori::class,
            'produk_kategori',
            'produk_id',
            'kategori_id'
        )->withTimestamps();
    }

    /**
     * Local scope: hanya produk aktif.
     *   Produk::aktif()->get()
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Format harga ke "Rp 1.240.000".
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga, 0, ',', '.');
    }
}
