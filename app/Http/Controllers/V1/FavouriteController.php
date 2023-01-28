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
        return Favourite::all();
    }
}
