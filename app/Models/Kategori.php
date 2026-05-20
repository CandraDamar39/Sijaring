<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
    ];

    /**
     * Many-to-many: kategori ↔ produk.
     */
    public function produks(): BelongsToMany
    {
        return $this->belongsToMany(
            Produk::class,
            'produk_kategori',
            'kategori_id',
            'produk_id'
        )->withTimestamps();
    }
}
