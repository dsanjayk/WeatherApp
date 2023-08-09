<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $apiKey = config('services.open_weather_map.api_key');
        $city = 'Gwalior'; // Change this to your desired city

        $cacheKey = 'weather_data';

        // Check if data exists in cache
        if (Redis::exists($cacheKey)) {
            $weatherData = Redis::get($cacheKey);
            return response()->json(json_decode($weatherData));
        }

        // Fetch data from weather API
        $response = Http::get("http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey");
        $weatherData = $response->json();

        // Store data in Redis cache for 10 minutes
        Redis::setex($cacheKey, 600, json_encode($weatherData));

        return response()->json($weatherData);
    }
}
