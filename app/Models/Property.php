<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'address', 'city', 'state', 'square_feet', 'monthly_rent',
        'description', 'is_available', 'note', 'photo', 'user_id','avg_rating',
        'latitude', 'longitude', 'views'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'avg_rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

public function favoritedByTenants()
{
    return $this->belongsToMany(
        Tenant::class,
        'favorites',
        'property_id',
        'tenant_id'
    )->withTimestamps();
}

   public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        
        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude))
        ))";

        return $query->selectRaw("$haversine AS distance", [$latitude, $longitude, $latitude])
                     ->whereRaw("$haversine <= ?", [$radius])
                     ->orderBy('distance', 'asc');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

  
}
