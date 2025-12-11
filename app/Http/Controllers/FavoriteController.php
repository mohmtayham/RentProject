<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;

class FavoriteController extends Controller
{
    public function index()
    {
        return Favorite::paginate(20);
    }

    public function show(Favorite $favorite)
    {
        return $favorite;
    }

    public function store(StoreFavoriteRequest $request)
    {
        $favorite = Favorite::create($request->validated());
        return response()->json($favorite, 201);
    }

    public function update(UpdateFavoriteRequest $request, Favorite $favorite)
    {
        $favorite->fill($request->validated());
        $favorite->save();
        return $favorite;
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();
        return response()->noContent();
    }
}
