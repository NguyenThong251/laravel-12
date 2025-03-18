<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = ['slug', 'price_per_night', 'star_rating', 'image'];

    public function translations(): HasMany
    {
        return $this->hasMany(HotelTranslation::class);
    }
    public function getTranslation($locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
