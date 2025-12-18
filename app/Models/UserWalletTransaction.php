<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWalletTransaction extends Model
{
    use HasFactory;
    protected $table = 'userwalletsactions';  // بدون underscore

    protected $fillable = ['wallet_id', 'type', 'amount', 'description'];

    public function wallet()
    {
        return $this->belongsTo(UserWallet::class);
    }
}
