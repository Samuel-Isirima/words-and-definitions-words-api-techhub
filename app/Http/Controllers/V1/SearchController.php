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

        $word = $request->word;

        //Get user
        $user = UserAuthController::getUser($request);

        if(!!$user)
        {
            $search = Search::create([
                'user_id' => $user->id,
                'word' => $word,
            ]);   
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
        ], Response::HTTP_OK);
    }


public function getSearches(Request $request)
{
    $user = UserAuthController::getUser($request);

    $searches = $user->searches()->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Fetch previous searches request successful',
        'user' => $user,
        'data' => $searches,
    ], Response::HTTP_OK);
}

}
