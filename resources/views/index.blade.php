@extends('layouts.app')

@section('content')
    <h2>Danh sách khách sạn</h2>
    <div class="hotel-list">
        @foreach($hotels as $hotel)
            @php
                $translation = $hotel->getTranslation($locale);
            @endphp
            <div class="hotel-card">
                <img src="{{ $hotel->image ? asset('storage/' . $hotel->image) : 'https://via.placeholder.com/300x150' }}" alt="{{ $translation->name }}">
                <h3>{{ $translation->name }}</h3>
                <p>{{ $translation->description }}</p>
                <p><strong>Địa chỉ:</strong> {{ $translation->address }}</p>
                <p class="price">{{ number_format($hotel->price_per_night, 2) }} USD / đêm</p>
                <p class="rating">
                    @for($i = 0; $i < $hotel->star_rating; $i++)
                        ★
                    @endfor
                </p>
            </div>
        @endforeach
    </div>
@endsection