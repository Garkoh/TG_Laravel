<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
//
//Route::get('/redis-test', function () {
//    Redis::set('ping', 'pong');
//    $value = Redis::get('ping');
//    return response()->json(['redis_value' => $value]);
//});


//use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Redis;

Route::get('/user/{username}', function ($username) {
    $username = strtolower(ltrim($username, '@'));

    $userData = Redis::hgetall("user:{$username}");

    if (empty($userData)) {
        return response()->json(['error' => 'User not found'], 404);
    }

    return response()->json($userData);
});
