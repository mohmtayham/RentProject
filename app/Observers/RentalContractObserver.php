<?php

namespace App\Observers;

use App\Models\RentalContract;
use App\Models\Property;

class RentalContractObserver
{
    /**
     * Handle the RentalContract "created" event.
     */
    public function created(RentalContract $contract): void
    {
        $this->recalculatePropertyAvg($contract->property_id);
    }

    /**
     * Handle the RentalContract "updated" event.
     */
    public function updated(RentalContract $contract): void
    {
        $this->recalculatePropertyAvg($contract->property_id);
    }

    /**
     * Handle the RentalContract "deleted" event.
     */
    public function deleted(RentalContract $contract): void
    {
        $this->recalculatePropertyAvg($contract->property_id);
    }

    protected function recalculatePropertyAvg($propertyId): void
    {
        if (!$propertyId) {
            return;
        }

        $avg = RentalContract::where('property_id', $propertyId)->avg('rate');

        $property = Property::find($propertyId);
        if (!$property) {
            return;
        }

        // If there are no contracts the avg will be null
        $property->avg_rating = $avg ? round($avg, 2) : 0;
        $property->save();
    }
}
