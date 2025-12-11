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
        'description', 'is_available', 'note', 'photo', 'landlord_id'
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function rentalContracts(): HasMany
    {
        return $this->hasMany(RentalContract::class);
    }

   
}
