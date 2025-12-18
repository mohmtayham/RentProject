<?php
namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Property;
use App\Models\Admin;
use App\Models\Landlord;
use Carbon\Carbon;
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
         $startDate = Carbon::now()->addDays($this->faker->numberBetween(1, 30));
        return [
            'tenant_id' => Tenant::factory(),
            'property_id' => Property::factory(),
            'landlord_id' => $this->faker->optional()->passthrough(Landlord::factory()),
               'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+1 year'),
            'monthly_rent' => $this->faker->numberBetween(1000, 5000),
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