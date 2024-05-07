<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth; // Menggunakan fasad JWTAuth
use App\Http\Controllers\Controller; // Menggunakan Controller
use Illuminate\Http\Request; // Menggunakan kelas Request dari Illuminate
use App\Models\FoodModel; // Menggunakan model FoodModel
use Illuminate\Support\Facades\Validator; // Menggunakan Validator dari Illuminate
use Illuminate\Support\Facades\Auth; // Menggunakan fasad Auth
use Illuminate\Support\Facades\Storage; // Menggunakan fasad Storage



class FoodController extends Controller
{
    /**
     * Menampilkan daftar makanan.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $search = null)
    {
        // Jika terdapat pencarian, cari makanan sesuai dengan kriteria

        if ($search) {
            $foods = FoodModel::where('name', 'like', '%' . $search . '%')
                ->orWhere('spicy_level', 'like', '%' . $search . '%')
                ->get();
            // Jika tidak ada pencarian, ambil semua makanan

        } else {
            $foods = FoodModel::all();
        }
        // Jika tidak ada makanan ditemukan, kembalikan respons dengan status 404

        if ($foods->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No food found'
            ], 404);
        }
        // Jika berhasil, kembalikan respons dengan data makanan

        return response()->json([
            'status' => true,
            'data' => $foods,
            'message' => 'Food has retrieved'
        ], 200);
    }
    /**
     * Menambahkan menu makanan baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addMenu(Request $request)
    {
        // Aturan validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'spicy_level' => 'required|in:Mild,Medium,Spicy',
            'price' => 'required|numeric|min:0',
            'image' => 'required',
        ]);

        // Jika validasi gagal, kembalikan respons dengan status 400
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Periksa apakah terdapat token bearer dalam request
        if (!$token = $request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi token bearer
        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Handle unggah file gambar
        // $imageName = time() . '-' . $request->image->getClientOriginalName();
        // $request->image->move(public_path('images'), $imageName);

        // Buat item makanan baru
        $food = new FoodModel([
            'name' => $request->name,
            'spicy_level' => $request->spicy_level,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        $food->save();
        // Kembalikan respons dengan status 201

        return response()->json([
            'status' => true,
            'data' => $food,
            'message' => 'Food has been created'
        ], 201);
    }
    /**
     * Memperbarui menu makanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_food
     * @return \Illuminate\Http\Response
     */
    public function updateMenu(Request $request, $id_food)
    {
        // Validasi data request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'spicy_level' => 'required',
            'price' => 'required|numeric',
            'image' => 'required',
        ]);

        // Jika validasi gagal, kembalikan respons dengan status 400
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Periksa apakah terdapat token bearer dalam request
        if (!$token = $request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi token bearer
        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Periksa apakah item makanan ada
        $food = FoodModel::find($id_food);

        if (!$food) {
            return response()->json(['error' => 'Food not found'], 404);
        }

        // Update data item makanan
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

        // Simpan perubahan
        $food->save();

        // Kembalikan respons dengan status 200
        return response()->json([
            'status' => true,
            'data' => $food,
            'message' => 'Food has been updated'
        ]);
    }


    /**
     * Menghapus menu makanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMenu(Request $request, $id)
{
    // Cari makanan berdasarkan ID

    $food = FoodModel::find($id);

    // Jika makanan tidak ditemukan, kembalikan respons dengan status 404

    if (!$food) {
        return response()->json(['error' => 'Food not found'], 404);
    }

    // Periksa apakah terdapat token bearer dalam request
    if (!$token = $request->bearerToken()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Validasi token bearer
    if (!JWTAuth::parseToken()->authenticate()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Periksa apakah pengguna yang diautentikasi adalah admin (role_id = 1)
    if (Auth::check() && Auth::user()->hasRole(1)) {
        // Jika ya, hapus makanan

        $food->delete();
        return response()->json(['message' => 'Food deleted successfully'], 200);
        // Jika tidak, kembalikan respons dengan status 401

    } else {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
}
