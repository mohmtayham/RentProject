<?php

namespace App\Http\Controllers;

use App\Models\Landlord;
use App\Http\Requests\StoreLandlordRequest;
use App\Http\Requests\UpdateLandlordRequest;

class LandlordController extends Controller
{
    public function index()
    {
        return Landlord::paginate(20);
    }

    public function show(Landlord $landlord)
    {
        return $landlord;
    }

    public function store(StoreLandlordRequest $request)
    {
        $landlord = Landlord::create($request->validated());
        return response()->json($landlord, 201);
    }

    public function update(UpdateLandlordRequest $request, Landlord $landlord)
    {
        $landlord->fill($request->validated());
        $landlord->save();
        return $landlord;
    }

    public function destroy(Landlord $landlord)
    {
        $landlord->delete();
        return response()->noContent();
    }
}
