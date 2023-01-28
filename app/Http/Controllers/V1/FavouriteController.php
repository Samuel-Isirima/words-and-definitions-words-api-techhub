<?php

namespace App\Http\Controllers\V1;

use App\Models\Favourite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavouriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    public function all($request)
    {
        $user = Auth::user();
        $favourites = Favourite::where('user_id', $user->id)->get();
    }

    public function add(Request $request)
    {
        $request->validate([
            'word' => 'required',
        ]);

        $favourite = Favourite::create($request->all());

        return response()->json([
            "success" => true,
            "message" => "PFavourite added successfully.",
            "data" => $favourite
            ]);
    }

    public function delete(Favourite $favourite)
    {
        $favourite->delete();

        return response()->json([
            "success" => true,
            "message" => "Favourite deleted successfully.",
            "data" => $favourite
            ]);
    }
}
