<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RentalContract extends Model
{
    use HasFactory;
    
     // ✅ Explicit table name

    protected $fillable = [
        'application_id',
        'property_id',
        'tenant_id',
        'landlord_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'rate',
        'status'
    ];

    

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime',
        'monthly_rent' => 'decimal:2',
        'rate' => 'integer',
        'security_deposit' => 'decimal:2',
    ];

   
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

  
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}