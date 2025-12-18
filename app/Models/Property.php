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
        'description', 'is_available', 'note', 'photo', 'landlord_id','avg_rating'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'avg_rating' => 'decimal:2',
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
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

   

   
}
