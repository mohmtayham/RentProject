<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Landlord extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'total_properties_managed'];

    // RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function propertyModifications(): HasMany
    {
        return $this->hasMany(Property::class);
    }
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
    public function userwallet(): HasMany
    {
        return $this->hasMany(Userwallet::class);
    }
}