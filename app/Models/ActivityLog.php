<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'old_data',
        'new_data',
        'ip_address',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quick helper to log an activity.
     */
    public static function log(
        string $action,
        string $description,
        ?string $model = null,
        ?string $modelId = null,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model'       => $model,
            'model_id'    => $modelId,
            'description' => $description,
            'old_data'    => $oldData,
            'new_data'    => $newData,
            'ip_address'  => request()->ip(),
        ]);
    }
}
