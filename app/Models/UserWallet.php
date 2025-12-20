<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userwallet extends Model
{
    use HasFactory;

    protected $table = 'userwallets'; // Explicitly set table name
    
    protected $fillable = [
        'tenant_id',
        'landlord_id',
        'balance'
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }
    public function transactions()
    {
        return $this->hasMany(UserWalletTransaction::class, 'userwallet_id');
    }
}