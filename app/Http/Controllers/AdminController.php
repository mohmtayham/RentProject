<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;

class AdminController extends Controller
{
    public function index()
    {
        return Admin::paginate(20);
    }

    public function show(Admin $admin)
    {
        return $admin;
    }

    public function store(StoreAdminRequest $request)
    {
        $admin = Admin::create($request->validated());
        return response()->json($admin, 201);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin->fill($request->validated());
        $admin->save();
        return $admin;
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->noContent();
    }
}
