<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $fillable = [
        'user_id', 'name', 'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
