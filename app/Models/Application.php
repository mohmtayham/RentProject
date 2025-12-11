<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model

{
    use HasFactory;
    
    /**
     * Return a new factory instance for the model.
     */
   protected $table = 'applications';
    protected $fillable = [
        'tenant_id', 
        'property_id', 
        'admin_id', 
        'status', 
        'submitted_at', 
       
        'notes'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

   
    public function rentalContract(): HasOne
    {
        return $this->hasOne(RentalContract::class);
    }

  
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

  
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}