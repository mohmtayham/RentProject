<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'user_type'
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
}