<?php

namespace App\Imports;

use App\Models\Hotel;
use App\Models\HotelTranslation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HotelsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Kiểm tra nếu slug đã tồn tại
        $hotel = Hotel::firstOrCreate(
            ['slug' => $row['slug']],
            [
                'price_per_night' => $row['price_per_night'],
                'star_rating' => $row['star_rating'],
                'image' => $row['image'],
            ]
        );

        // Thêm bản dịch tiếng Việt
        HotelTranslation::updateOrCreate(
            ['hotel_id' => $hotel->id, 'locale' => 'vi'],
            [
                'name' => $row['name_vi'],
                'description' => $row['description_vi'],
                'address' => $row['address_vi'],
            ]
        );

        // Thêm bản dịch tiếng Anh
        HotelTranslation::updateOrCreate(
            ['hotel_id' => $hotel->id, 'locale' => 'en'],
            [
                'name' => $row['name_en'],
                'description' => $row['description_en'],
                'address' => $row['address_en'],
            ]
        );

        return $hotel;
    }

    /**
     * Định nghĩa các quy tắc validation
     */
    public function rules(): array
    {
        return [
            'slug' => 'required|unique:hotels,slug',
            'price_per_night' => 'required|numeric|min:0',
            'star_rating' => 'required|integer|between:1,5',
            'image' => 'nullable|string',
            'name_vi' => 'required|string|max:255',
            'description_vi' => 'required|string',
            'address_vi' => 'required|string',
            'name_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'address_en' => 'required|string',
        ];
    }
}
