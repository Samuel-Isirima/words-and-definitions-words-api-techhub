<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /*

        This middleware will be reqistered as a global middleware, so it runs on every request to the server

        The way to do this location restriction is this
        1. Get IP address
        2. Send request to ipinfo.io api to get details
        3. Restrict based on that
        */

        
        $userIPAddress = "197.210.54.159"; //for testing
        //$userIPAddress = $request->ip();

        $response = Http::withHeaders([
            'Accept' => 'application/json', 
            'Authorization' => "Bearer ".$_ENV['IPINFO_API_KEY'],
        ])->get("https://ipinfo.io/".$userIPAddress);

        if(!$response->successful())
        {
            /* 
            Log error, don't block user
            */    
        }
    

        $data = $response->json();
        if(!array_key_exists("country", $data))
        {
            return response()->json([
                'message' => 'This application needs to know your country before accepting requests from you.
                              If you are in a local development environment (ie your IP address is 127.0.0.1, 
                              please change it to a valid ip address using any tool of your choice and retry.',
                'data' => $data,
            ], Response::HTTP_FORBIDDEN);   
        }
        $country = $data['country'];

        //Allowed countries array
        $countries = ["NG","US"];
        if(!in_array($country, $countries))
        {
      
            return response()->json([
                'message' => 'This application is not supported in your country.',
                'data' => $data,
            ], Response::HTTP_FORBIDDEN);

        }

      

        
        
        return $next($request);
    }
}
