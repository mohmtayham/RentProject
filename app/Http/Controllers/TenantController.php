<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;

class TenantController extends Controller
{
    public function index()
    {
        return Tenant::paginate(20);
    }

    public function show(Tenant $tenant)
    {
        return $tenant;
    }

    public function store(StoreTenantRequest $request)
    {
        $tenant = Tenant::create($request->validated());
        return response()->json($tenant, 201);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant->fill($request->validated());
        $tenant->save();
        return $tenant;
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return response()->noContent();
    }
}
