<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class FoodController extends Controller
{
    /**
     * Display a listing of the food.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $search = null)
    {
        if ($search) {
            $foods = FoodModel::where('name', 'like', '%' . $search . '%')
                ->orWhere('spicy_level', 'like', '%' . $search . '%')
                ->get();
        } else {
            $foods = FoodModel::all();
        }

        if ($foods->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No food found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $foods,
            'message' => 'Food has retrieved'
        ], 200);
    }
    public function addMenu(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'spicy_level' => 'required|in:Mild,Medium,Spicy',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the request has a bearer token
        if (!$token = $request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the bearer token
        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Handle file upload
        $imageName = time() . '-' . $request->image->getClientOriginalName();
        $request->image->move(public_path('images'), $imageName);

        // Create new food item
        $food = new FoodModel([
            'name' => $request->name,
            'spicy_level' => $request->spicy_level,
            'price' => $request->price,
            'image' => $imageName,
        ]);

        $food->save();

        return response()->json([
            'status' => true,
            'data' => $food,
            'message' => 'Food has been created'
        ], 201);
    }
    public function updateMenu(Request $request, $id_food)
{
    // Validate request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'spicy_level' => 'required',
        'price' => 'required|numeric',
        'image' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Check if food item exists
    $food = FoodModel::find($id_food);

    if (!$food) {
        return response()->json(['error' => 'Food not found'], 404);
    }

    // Update food item data
    $food->name = $request->name;
    $food->spicy_level = $request->spicy_level;
    $food->price = $request->price;
    $food->image = $request->image;

    // Handle image upload if provided
    // if ($request->hasFile('image')) {
    //     $imageName = time() . '-' . $request->image->getClientOriginalName();
    //     $request->image->move(public_path('images'), $imageName);
    //     $food->image = $imageName;
    // }

    $food->save();

    return response()->json([
        'status' => true,
        'data' => $food,
        'message' => 'Food has been updated'
    ]);
}


    public function deleteMenu($id)
    {
        $food = FoodModel::find($id);

        if (!$food) {
            return response()->json(['error' => 'Food not found'], 404);
        }

        // Check if the authenticated user is an admin (role_id = 1)
        if (Auth::user()->hasRole(1)) {
            $food->delete();
            return response()->json(['message' => 'Food deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

}
