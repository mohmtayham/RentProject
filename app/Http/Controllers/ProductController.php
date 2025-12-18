<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'name'    => $request->name,
            'price'   => $request->price,
            'user_id' => $user_id,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }
    public function destroy($id)
    {
        $user = Auth::user();




        $product = Product::findOrFail($id);
        if ($product->user_id == $user->id) {
            return response()->json([
                'message' => 'you are not the owner of this product'
            ]);
        }


        $product->delete();

        return response()->json([
            'message' => 'product deleted successfully'
        ]);
    }
}
