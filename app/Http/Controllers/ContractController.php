<?php

namespace App\Http\Controllers;

use App\Http\Resources\RentalContractResource;
use App\Models\Contract;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;

class ContractController extends Controller
{
public function index(){

$rentalContracts = Contract::all();
return RentalContractResource::collection($rentalContracts);


}
}
