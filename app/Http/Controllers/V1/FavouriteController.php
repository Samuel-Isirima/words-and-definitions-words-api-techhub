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


    public function all(Request $request)
    {
        $user = UserAuthController::getUser($request);

        $favourites = $user->favourites()->get();

        return response()->json([
            "success" => true,
            "message" => "Favourites fetched successfully.",
            "data" => $favourites
            ], Response::HTTP_OK);
    }


    public function add(Request $request)
    {
        $user = UserAuthController::getUser($request);
        
        $request->validate([
            'word' => ['required', new UniqueFavouritesForEachUser],
        ]);

        $favourite = Favourite::create([
            'word' => $request->word,
            'user_id' => $user->id,
        ]);

        return response()->json([   
            "success" => true,
            "message" => "Favourite added successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }


    public function delete($favouriteID)
    {
        $favourite->delete();

        return response()->json([
            "success" => true,
            "message" => "Favourite deleted successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }


    public function details($favouriteID)
    {
        return response()->json([
            "success" => true,
            "message" => "Favourite fetched successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }
}
