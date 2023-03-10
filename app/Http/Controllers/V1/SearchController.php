<?php

namespace App\Http\Controllers\V1;

use App\Models\Search;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\V1\UserAuthController;

class SearchController extends Controller
{
   
    public function search(Request $request)
    {
        $request->validate([
            'word' => 'required|string',
        ]);

        $word = strtolower($request->word);

        //Get user
        $user = UserAuthController::getUser($request);
        $word_in_favourite = false;

        if(!!$user)
        {
            $search = Search::create([
                'user_id' => $user->id,
                'word' => $word,
            ]);   

            $word_in_favourite = !!\App\Models\Favourite::where('word', '=', $word)
            ->where('user_id', '=', $user->id)
            ->first();
        }

        $response = Http::withHeaders([
            'app_id' => 'ceac2c2c', 
            'app_key' => $_ENV['DICTIONARY_API_KEY'],
        ])->get('https://od-api.oxforddictionaries.com/api/v2/entries/en-us/'.$word);


        
        if(!$response->successful())
        {
            $message = 'An error occured while trying to make the search request. Please try again later.';
            switch($response->status())
            {
                case 404:
                    {
                        $message = 'The word you are searching for does not exist in our dictionary.';
                        break;
                    }
                case 503:
                    {
                        $message = 'Our servers are up, but overloaded with requests. Please try again later.';
                        break;
                    }
                case 504:
                    {
                        $message = ' The request couldn’t be serviced due to some failure within our stack. Please try again later.';
                        break;
                    }
            }

            return response()->json([
                'status' => 'Error',
                'message' => $message,
            ], $response->status());
        }

    

        $jsonData = $response->json();
        /*
        The sample api response from the dictionary api has a structure that requires the kind of 
        value assignment below.
        Study the sample-dictionary-api-response.json file to understand
        */
        $senses_array = $jsonData['results'][0]['lexicalEntries'][0]['entries'][0]['senses'];
        $definitions_array = [];

        foreach($senses_array as $sense)
        {
            if(array_key_exists('definitions', $sense))
            {
                $vol_array = [];
                $vol_array['definition'] = $sense['definitions'];

                if(array_key_exists('examples', $sense))
                    $vol_array['examples'] = $sense['examples'];
                
                array_push($definitions_array, $vol_array);
            }
        }
        

        return response()->json([
            'status' => 'success',
            'message' => 'Search request carried out successfully',
            'user' => $user,
            'data' => $definitions_array,
            'in_favourites' => $word_in_favourite
        ], Response::HTTP_OK);
    }


public function getSearches(Request $request)
{
    $user = UserAuthController::getUser($request);

    $searches = $user->searches()->orderBy('created_at', 'desc')->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Fetch previous searches request successful',
        'user' => $user,
        'data' => $searches,
    ], Response::HTTP_OK);
}


public function delete(Request $request, $id)
{
    $user = UserAuthController::getUser($request);

    $search = Search::find($id);

    if(!$search)
    {
        return response()->json([
            "success" => false,
            "message" => "Previous search could not be found",
            ], Response::HTTP_BAD_REQUEST);
    }

    $search->delete();

    $searches = $user->searches()->orderBy('created_at', 'desc')->get();

    return response()->json([
        "success" => true,
        "message" => $search->word." has been deleted from your search history successfully.",
        "searches" => $searches,
        ], Response::HTTP_OK);
}


}
