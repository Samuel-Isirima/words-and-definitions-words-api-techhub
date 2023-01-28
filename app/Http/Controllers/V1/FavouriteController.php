<?php
/* 
Hi, My name is Samuel Isirima
I love building software; APIs, web apps, mobile apps, desktop apps, SDKs.

I'm one of the best drivers you've ever known. :)
*/
namespace App\Http\Controllers\V1;

use App\Models\Favourite;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Rules\UniqueFavouritesForEachUser;

class FavouriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function all($request)
    {
        $user = Auth::user();
        $favourites = Favourite::where('user_id', $user->id)->get();
        return response()->json([
            "success" => true,
            "message" => "Favourites fetched successfully.",
            "data" => $favourites
            ], Response::HTTP_OK);
    }

    public function add(Request $request)
    {
        $request->validate([
            'word' => ['required', new UniqueFavouritesForEachUser],
        ]);

        $favourite = Favourite::create([
            'word' => $request->word,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json([   
            "success" => true,
            "message" => "Favourite added successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }


    public function delete(Favourite $favourite)
    {
        $favourite->delete();

        return response()->json([
            "success" => true,
            "message" => "Favourite deleted successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }


    public function details(Favourite $favourite)
    {
        return response()->json([
            "success" => true,
            "message" => "Favourite fetched successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }
}
