<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;

class ApplicationController extends Controller
{
public function index(){
$applications = Application::all();
return ApplicationResource::collection($applications);


}

}
