<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
   
    public function search(Request $request)
    {
        $request->validate([
            'word' => 'required|string',
        ]);

        $word = $request->word;

        $response = Http::withHeaders([
            'X-Mashape-Key' => '02a969d6f1mshb63c854a6caccd1p1526fejsn1bfd5b231952', //$_ENV['WORDS_API_KEY'],
            'X-RapidAPI-Host' => 'wordsapiv1.p.rapidapi.com',
        ])->get('https://wordsapiv1.p.rapidapi.com/words/'.$word.'/definitions');

        if(!$response->successful())
        {
            return response()->json([
                'status' => 'Error',
                'message' => 'An error occured while trying to make search request',
            ], 500);
        }

        $jsonData = $response->json();

        return response()->json([
            'status' => 'success',
            'message' => 'Search request carried out successfully',
            'user' => $user,
            'data' => $jsonData,
        ]);
    }
}
