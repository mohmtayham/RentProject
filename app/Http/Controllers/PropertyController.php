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
public function showAvillableProperties()
{
    $properties = Property::where('is_available', true)->get();
    return PropertyResource::collection($properties);
}

public function showPropertyDetails($id)
{
    $property = Property::findOrFail($id);
    return new PropertyResource($property);
}

public function updatePropertyDetails(Request $request,$id)
{

    $property = Property::findOrFail($id);

    $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'city' => 'sometimes|string|max:100',
        'address' => 'sometimes|string|max:255',
        'monthly_rent' => 'sometimes|numeric',
        'is_available' => 'sometimes|boolean',
    ]);

    $property->update($request->only([
        'title', 'description', 'city', 'address', 'monthly_rent', 'is_available'
    ]));

    return new PropertyResource($property);
}
public function destroy($id)
{
    $property = Property::findOrFail($id);
    $property->delete();

    return response()->json([
        'message' => 'Property deleted successfully'
    ]);

}
public function addToFavorites(Request $request, $id)
{
    $property = Property::findOrFail($id);
    $user = $request->user();

    $user->favoriteProperties()->attach($property->id);

    return response()->json([
        'message' => 'Property added to favorites'
    ], 200);
}

public function removeFromFavorites(Request $request, $id)
{
    $property = Property::findOrFail($id);
    $user = $request->user();

    $user->favoriteProperties()->detach($property->id);

    return response()->json([
        'message' => 'Property removed from favorites'
    ], 200);
}
public function listFavoriteProperties(Request $request)
{
    $user = $request->user();
    $favoriteProperties = $user->favoriteProperties;

    return PropertyResource::collection($favoriteProperties);


}






}
