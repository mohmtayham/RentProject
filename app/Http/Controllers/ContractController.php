<?php

namespace App\Http\Controllers;

use App\Http\Resources\RentalContractResource;
use App\Models\Contract;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\RentalContract;
use App\Models\Tenant;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class ContractController extends Controller
{
public function index(){

  $rentalContracts = RentalContract::all(); 
return RentalContractResource::collection($rentalContracts);


}
public function store(Request $request)
{
 

  // Validate input
    $validated = $request->validate([
        'application_id' => 'required|exists:applications,id',
        'property_id' => 'required|exists:properties,id',
        'tenant_id' => 'required|exists:users,id',
        'landlord_id' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'monthly_rent' => 'required|numeric',
        'rate' => 'nullable|integer|min:1|max:5',
        'status' => 'required|in:active,terminated,pending',
    ]);

    // Create contract
    $contract = RentalContract::create($validated);

    return new RentalContractResource($contract);
}
public function addContract(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'application_id' => 'required|exists:applications,id',
        
        'rate' => 'nullable|integer|min:1|max:5',
        'status' => 'required|in:active,terminated,pending',
    ]);


 
    // Create contract
    $contract = RentalContract::create($validated);

    return new RentalContractResource($contract);
}


public function show(Request $request)
{
    $rentalContracts= RentalContract::findOrFail($request->id);
    return new RentalContractResource($rentalContracts);
}public function edit(Request $request)
{
    
    $validated = $request->validate([
       'application_id' => 'sometimes|exists:applications,id',
        'end_date' => 'sometimes|date|after:start_date',
        'monthly_rent' => 'sometimes|numeric',
        'rate' => 'nullable|integer|min:1|max:5',
        'status' => 'sometimes|in:draft,active,expired,terminated',
    ]);

   
    $contract = RentalContract::findOrFail($request->id);
    $contract->update($validated);

    return new RentalContractResource($contract);
}

public function addrate(Request $request, $id)
{
    $request->validate([
        'rate' => 'required|integer|min:1|max:5',
    ]);

    $rentalContract = RentalContract::findOrFail($id);
    $rentalContract->rate = $request->rate;
    $rentalContract->save();

    return new RentalContractResource($rentalContract);
}


public function editrate(Request $request, $id)
{
    $request->validate([
        'rate' => 'required|integer|min:1|max:5',
    ]);

    $rentalContract = RentalContract::findOrFail($id);
    $rentalContract->rate = $request->rate;
    $rentalContract->save();

    return new RentalContractResource($rentalContract);
}



public function editContractstatus(Request $request)
{
$request->validate([
    'status' => 'required|in:draft,active,expired,terminated'
]);
$rentalContract = RentalContract::findOrFail($request->id);
$rentalContract->status = $request->status;
$rentalContract->save();
return new RentalContractResource($rentalContract);


}
//'draft', 'active', 'expired', 'terminated'







}
// if(Auth::user()->role !== 'tenant'){
//     return response()->json(['message' => 'Only tenants can request contract edits.'], 403);