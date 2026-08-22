<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Hpp extends Model
{
    protected $table = 'hpp';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'jumlah',
        'kategori',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'bahan_baku'  => 'Bahan Baku',
            'operasional' => 'Operasional',
            'lainnya'     => 'Lainnya',
            default       => $this->kategori,
        };
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }
}