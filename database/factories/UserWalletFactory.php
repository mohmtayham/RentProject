<?php

namespace Database\Factories;

use App\Models\Landlord;
use App\Models\Userwallet;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserWalletFactory extends Factory
{
    protected $model = Userwallet::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(), // Creates associated tenant
            'landlord_id' => Landlord::factory(), // Creates associated landlord
            'balance' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}