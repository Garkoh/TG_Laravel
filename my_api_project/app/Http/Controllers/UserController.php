<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class UserController extends Controller
{
    public function daysToBirthday($id): JsonResponse
    {
        $data = Redis::get("user:$id");

        if (!$data)
        {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user = json_decode($data, true);

        if (!isset($user['birthday'])) {
            return response()->json(['error' => 'Birthday not found for user'], 400);
        }

        $birthday = Carbon::parse($user['birthday']);
        $now = Carbon::today();

        $birthday->year($now->year);
        if ($birthday->isPast()) {
            $birthday->addYear();
        }

        $daysLeft = $now->diffInDays($birthday);

        return response()->json([
            'id' => $user['id'] ?? $id,
            'name' => $user['name'] ?? 'Unknown',
            'days_to_birthday' => $daysLeft,
        ]);
    }
}
