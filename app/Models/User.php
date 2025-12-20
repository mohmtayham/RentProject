<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'user_type',
        'photo',
        'id_photo',
        'address',
        'status',
    ];
   

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // You're already using Hash::make(), so this is fine
    ];

    // RELATIONSHIPS
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function landlord(): HasOne
    {
        return $this->hasOne(Landlord::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }
 
   
    public function profile()
    {
        return $this->{$this->user_type}();
    }

    // Role helpers
    public function hasRole($role)
    {
        return $this->user_type === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isLandlord()
    {
        return $this->hasRole('landlord');
    }

    public function isTenant()
    {
        return $this->hasRole('tenant');
    }

    // Optionally keep related profile in sync when changing user_type
    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->isDirty('user_type')) {
                $old = $user->getOriginal('user_type');
                $new = $user->user_type;
                if ($old && $user->{$old}) {
                    try { $user->{$old}->delete(); } catch (\Throwable $e) {}
                }
                if ($new && ! $user->{$new}) {
                    try { $user->{$new}()->create([]); } catch (\Throwable $e) {}
                }
            }
        });
    }

    public function isApproved()
{
    return $this->status === 'approved';
}
   
public function favoriteProperties()
{
    return $this->belongsToMany(Property::class, 'properties')
                ->withTimestamps();
}

    // Friends relations
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
                    ->wherePivot('accepted', true)
                    ->withTimestamps();
    }

    public function pendingFriendsSent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
                    ->wherePivot('accepted', false)
                    ->withTimestamps();
    }

    public function pendingFriendsReceived(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
                    ->wherePivot('accepted', false)
                    ->withTimestamps();
    }

    public function isFriendWith($userId)
    {
        return $this->friends()->where('friend_id', $userId)->exists();
    }

    public function hasSentRequestTo($userId)
    {
        return $this->pendingFriendsSent()->where('friend_id', $userId)->exists();
    }

    public function hasReceivedRequestFrom($userId)
    {
        return $this->pendingFriendsReceived()->where('user_id', $userId)->exists();
    }

public function approver()
{
    return $this->belongsTo(User::class, 'approved_by');
}
public function products()
{
    return $this->hasMany(Product::class);}
public function property(){
    return $this->hasMany(Property::class);
}

}
