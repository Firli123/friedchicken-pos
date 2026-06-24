<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name','username','email','password','role','is_active','avatar'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean'];

    public function transactions() { return $this->hasMany(Transaction::class); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isKasir(): bool { return $this->role === 'kasir'; }

    public function hasAccess(string $permission): bool {
        $owner = ['dashboard','products','reports','users','backup','restore','settings','pos','transactions','reprint'];
        $kasir = ['pos','transactions','reprint'];
        return in_array($permission, $this->isOwner() ? $owner : $kasir);
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
}