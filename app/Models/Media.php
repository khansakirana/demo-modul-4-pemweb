<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'year',
        'episodes',
        'country',
    ];

    protected $casts = [
        'year' => 'integer',
        'episodes' => 'integer',
    ];

    /**
     * Format ringkasan detail media sesuai konsep FlipPlay (Anime & Drama).
     */
    // [PENANDA ACCESSOR] Format detail Anime/Drama sesuai Modul 3
    protected function details(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->type === 'drama') {
                    return "[Drama] {$this->title} | Genre: {$this->genre} | Tahun: {$this->year} | Episode: {$this->episodes} | Negara: {$this->country}";
                }

                return "[Anime] {$this->title} | Genre: {$this->genre} | Tahun: {$this->year} | Episode: {$this->episodes}";
            }
        );
    }

    /**
     * Query scope untuk mencari media berdasarkan judul.
     */
    // [PENANDA QUERY SCOPE] Filter pencarian judul ?title=
    public function scopeSearchTitle(Builder $query, ?string $keyword): Builder
    {
        if ($keyword !== null && $keyword !== '') {
            return $query->where('title', 'like', '%' . $keyword . '%');
        }

        return $query;
    }

    /**
     * Query scope untuk memfilter media berdasarkan jenis (anime/drama).
     */
    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if ($type !== null && $type !== '') {
            return $query->where('type', strtolower($type));
        }

        return $query;
    }

    /**
     * Relasi many-to-many: satu Media dapat dikoleksi oleh banyak User.
     */
    // [PENANDA RELASI belongsToMany - MEDIA -> USERS]
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
