<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;

class PropertyController extends Controller
{
public function index()
{
    $properties = Property::all(); // Use proper variable name
    return PropertyResource::collection($properties); 
}
public function filterBycity($city)
{
    $properties = Property::where('city', $city)->get();
    return PropertyResource::collection($properties);
}
public function filterBymonthly_rent(Request $request)
{
    $minRent = $request->input('min_rent');
    $maxRent = $request->input('max_rent');

    $properties = Property::whereBetween('monthly_rent', [$minRent, $maxRent])->get();
    return PropertyResource::collection($properties);
}

public function make_ave_rationg($propertyId, Request $request)
{
    $property = Property::findOrFail($propertyId);
    $newRating = $request->input('rating');

    // Simple average calculation (you might want to store number of ratings for a better average)
    $property->avg_rating = ($property->avg_rating + $newRating) / 2;
    $property->save();

    return new PropertyResource($property);
}

}
