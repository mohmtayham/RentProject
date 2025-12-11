<?php
namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Property;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Application::class;
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'property_id' => Property::factory(),
            'admin_id' => $this->faker->optional()->passthrough(Admin::factory()),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'under_review']),
            'submitted_at' => now(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function pending()
    {
        return $this->state([
            'status' => 'pending',
            'admin_id' => null,
        ]);
    }

    public function approved()
    {
        return $this->state([
            'status' => 'approved',
        ]);
    }

    public function rejected()
    {
        return $this->state([
            'status' => 'rejected',
        ]);
    }
}