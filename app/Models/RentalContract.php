<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RentalContract extends Model
{
    use HasFactory;
    
  

    protected $fillable = [
        'application_id',    
        'rate',
        'status'
    ];

    

    protected $casts = [
       
        'rate' => 'integer',
        'security_deposit' => 'decimal:2',
    ];

   
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    
}