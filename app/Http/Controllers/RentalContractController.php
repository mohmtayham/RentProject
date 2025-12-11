<?php

namespace App\Http\Controllers;

use App\Models\RentalContract;
use App\Http\Requests\StoreRentalContractRequest;
use App\Http\Requests\UpdateRentalContractRequest;

class RentalContractController extends Controller
{
    public function index()
    {
        return RentalContract::paginate(20);
    }

    public function show(RentalContract $rentalContract)
    {
        return $rentalContract;
    }

    public function store(StoreRentalContractRequest $request)
    {
        $rentalContract = RentalContract::create($request->validated());
        return response()->json($rentalContract, 201);
    }

    public function update(UpdateRentalContractRequest $request, RentalContract $rentalContract)
    {
        $rentalContract->fill($request->validated());
        $rentalContract->save();
        return $rentalContract;
    }

    public function destroy(RentalContract $rentalContract)
    {
        $rentalContract->delete();
        return response()->noContent();
    }
}
