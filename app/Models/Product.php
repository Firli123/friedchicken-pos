<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'price',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'integer',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-product.png');
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        if ($categorySlug && $categorySlug !== 'all') {
            return $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }
        return $query;
    }

    // -------------------------------------------------------
    // Static Helpers
    // -------------------------------------------------------

    public static function generateCode(string $categorySlug): string
    {
        $prefix = match ($categorySlug) {
            'ayam'     => 'AYM',
            'paket'    => 'PKT',
            'tambahan' => 'TMB',
            default    => 'PRD',
        };

        $last = static::where('code', 'like', $prefix . '-%')
            ->orderByDesc('code')
            ->value('code');

        $num = $last ? (int) substr($last, -3) + 1 : 1;

        return $prefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}
