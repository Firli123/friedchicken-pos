<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'subtotal'      => 'integer',
        'discount'      => 'integer',
        'tax'           => 'integer',
        'total'         => 'integer',
        'amount_paid'   => 'integer',
        'change_amount' => 'integer',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format($this->total, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'CASH',
            'qris' => 'QRIS',
            default => strtoupper($this->payment_method),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'      => '<span class="badge bg-success">Lunas</span>',
            'pending'   => '<span class="badge bg-warning">Pending</span>',
            'failed'    => '<span class="badge bg-danger">Gagal</span>',
            'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
            default     => '<span class="badge bg-secondary">' . $this->payment_status . '</span>',
        };
    }

    // -------------------------------------------------------
    // Static Helpers
    // -------------------------------------------------------

    public static function generateNumber(): string
    {
        $today  = Carbon::now()->format('Ymd');
        $prefix = 'TRX-' . $today . '-';

        $last = static::where('number', 'like', $prefix . '%')
            ->orderByDesc('number')
            ->value('number');

        $num = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                     ->whereYear('created_at', Carbon::now()->year);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
