<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserWalletTransaction;
use App\Models\UserWallet;

class UserWalletTransactionFactory extends Factory
{
    protected $model = UserWalletTransaction::class;

    public function definition()
    {
        $types = ['deposit', 'withdraw', 'payment', 'refund'];
        return [
            'wallet_id' => UserWallet::factory(),
            'type' => $this->faker->randomElement($types),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
        ];
    }
}
