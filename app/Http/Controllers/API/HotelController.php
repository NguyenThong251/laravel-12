<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->query('lang', 'vi'); // Mặc định là tiếng Việt
        $hotels = Hotel::all();

        return view('index', compact('hotels', 'locale'));
    }
}
