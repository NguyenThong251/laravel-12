<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    // public function index(Request $request)
    // {
    //     $locale = $request->query('lang', 'vi'); // Mặc định là tiếng Việt
    //     $hotels = Hotel::all();

    //     return view('index', compact('hotels', 'locale'));
    // }
    public function index(Request $request)
    {
        $locale = $request->query('lang', 'vi'); // Ngôn ngữ mặc định là tiếng Việt
        $hotels = Hotel::with(['translations'])->get(); // Lấy tất cả khách sạn và bản dịch

        // Chuẩn bị dữ liệu trả về
        $data = $hotels->map(function ($hotel) use ($locale) {
            $translation = $hotel->getTranslation($locale) ?? $hotel->getTranslation('vi'); // Nếu không có ngôn ngữ yêu cầu, fallback về tiếng Việt

            return [
                'id' => $hotel->id,
                'slug' => $hotel->slug,
                'price_per_night' => $hotel->price_per_night,
                'star_rating' => $hotel->star_rating,
                'image' => $hotel->image ? asset('storage/' . $hotel->image) : 'https://via.placeholder.com/300x150',
                'name' => $translation->name,
                'description' => $translation->description,
                'address' => $translation->address,
                'locale' => $locale,
            ];
        });

        return response()->json([
            'message' => 'Danh sách khách sạn',
            'data' => $data,
        ], 200);
    }
}
