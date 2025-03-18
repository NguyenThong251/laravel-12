<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ bằng delete (thay vì truncate)
        HotelTranslation::query()->delete(); // Xóa dữ liệu trong hotel_translations trước
        Hotel::query()->delete(); // Xóa dữ liệu trong hotels

        // Khách sạn 1
        $hotel1 = Hotel::create([
            'slug' => Str::slug('sea-view-hotel'),
            'price_per_night' => 150.00,
            'star_rating' => 5,
            'image' => 'sea-view-hotel.jpg',
        ]);

        HotelTranslation::create([
            'hotel_id' => $hotel1->id,
            'locale' => 'vi',
            'name' => 'Khách sạn Sea View',
            'description' => 'Khách sạn 5 sao với tầm nhìn biển tuyệt đẹp, nằm tại trung tâm Đà Nẵng.',
            'address' => '123 Đường Biển, Đà Nẵng, Việt Nam',
        ]);

        HotelTranslation::create([
            'hotel_id' => $hotel1->id,
            'locale' => 'en',
            'name' => 'Sea View Hotel',
            'description' => 'A 5-star hotel with stunning sea views, located in the heart of Da Nang.',
            'address' => '123 Beach Road, Da Nang, Vietnam',
        ]);

        // Khách sạn 2
        $hotel2 = Hotel::create([
            'slug' => Str::slug('mountain-retreat'),
            'price_per_night' => 120.00,
            'star_rating' => 4,
            'image' => 'mountain-retreat.jpg',
        ]);

        HotelTranslation::create([
            'hotel_id' => $hotel2->id,
            'locale' => 'vi',
            'name' => 'Khách sạn Mountain Retreat',
            'description' => 'Khách sạn 4 sao nằm giữa thiên nhiên núi rừng, lý tưởng để thư giãn.',
            'address' => '456 Đường Núi, Đà Lạt, Việt Nam',
        ]);

        HotelTranslation::create([
            'hotel_id' => $hotel2->id,
            'locale' => 'en',
            'name' => 'Mountain Retreat Hotel',
            'description' => 'A 4-star hotel nestled in nature, perfect for relaxation.',
            'address' => '456 Mountain Road, Da Lat, Vietnam',
        ]);
    }
}
