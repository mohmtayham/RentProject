<?php
namespace Database\Seeders;

use App\Models\Application;
use App\Models\RentalContract;
use Illuminate\Database\Seeder;

class RentalContractSeeder extends Seeder
{
    public function run(): void
    {
        $approvedApplications = Application::where('status', 'approved')->get();

        foreach ($approvedApplications as $application) {
            RentalContract::factory()->create([
                'application_id' => $application->id,
               
                'rate' => rand(1, 5),
                'status' => 'active',
            ]);
        }

        RentalContract::factory()->count(5)->create();
    }
}