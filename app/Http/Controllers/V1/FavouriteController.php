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
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueFavouritesForEachUser;
use App\Http\Controllers\V1\UserAuthController;

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
        
        $validator = Validator::make($request->all(), 
        [
            'word' => ['required', Rule::unique('favourites')->where(fn ($query) => $query->where('user_id', $user->id))]
        ]);
        
        $word = $request->word;
        
        if ($validator->fails()) 
        {
            return response()->json([   
                "success" => false,
                "message" => $word." already exists in your favourites.",
                "word" => $word
                ], Response::HTTP_BAD_REQUEST);
        }

        $word = strtolower($request->word);

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


    public function delete(Request $request, $word)
    {
        $user = UserAuthController::getUser($request);

        $favourite = Favourite::where('word', '=', $word)
        ->where('user_id', '=', $user->id)
        ->first();


        if(!$favourite)
        {
            return response()->json([
                "success" => false,
                "message" => $word." is not in your favourites.",
                ], Response::HTTP_BAD_REQUEST);
        }

        $favourite->delete();

        $favourites = $user->favourites()->get();

        return response()->json([
            "success" => true,
            "message" => "Favourite deleted successfully.",
            "favourites" => $favourites,
            ], Response::HTTP_OK);
    }


    public function details($id)
    {
       
        $favourite = Favourite::find($id);

        if(!$favourite)
        {
            return response()->json([
                "success" => false,
                "message" => "Favourite does not exist.",
                ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            "success" => true,
            "message" => "Favourite fetched successfully.",
            "data" => $favourite
            ], Response::HTTP_OK);
    }
}
