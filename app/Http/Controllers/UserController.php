<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUser($userId)
    {
        $cacheKey = "user:$userId";

        // Check if data exists in cache
        if (Redis::exists($cacheKey)) {
            $userData = Redis::get($cacheKey);
            return response()->json(json_decode($userData));
        }

        // Fetching user data from the database
        $userData = DB::table('users')->where('id', $userId)->first();

        // Store user data in Redis cache for 5 minutes
        Redis::setex($cacheKey, 300, json_encode($userData));

        return response()->json($userData);
    }
}
