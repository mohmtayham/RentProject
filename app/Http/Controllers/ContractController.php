<?php

namespace App\Http\Controllers;

use App\Http\Resources\RentalContractResource;
use App\Models\Contract;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\RentalContract;
use App\Models\Tenant;
use Illuminate\Http\Request; // ✅ Correct Laravel Request class
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
public function addWithoutConflictInreservations(Request $request)
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

    // Check for reservation conflicts
    $conflict = RentalContract::where('property_id', $validated['property_id'])
        ->where(function ($query) use ($validated) {
            $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhere(function ($q) use ($validated) {
                      $q->where('start_date', '<=', $validated['start_date'])
                        ->where('end_date', '>=', $validated['end_date']);
                  });
        })
        ->exists();

    if ($conflict) {
        return response()->json(['message' => 'Reservation conflict detected.'], 409);
    }

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
       
        'property_id' => 'sometimes|exists:properties,id',
        'tenant_id' => 'sometimes|exists:users,id',
        'landlord_id' => 'sometimes|exists:users,id',
        'start_date' => 'sometimes|date',
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


public function destroyWithApprovalFromTenant(RentalContract $rentalContract)
{
    $rentalContract->delete();

    return response()->json(null, 204);



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


public function addWithApprovalFromLandlord(Request $request)
{
    // if(Auth::user()->role !== 'landlord'){
    //     return response()->json(['message' => 'Only landlords can create contracts.'], 403);
    // }

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

    $contract = RentalContract::create($validated);

    return new RentalContractResource($contract);   
}






}
// if(Auth::user()->role !== 'tenant'){
//     return response()->json(['message' => 'Only tenants can request contract edits.'], 403);