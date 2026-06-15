<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Test;


class RatingController extends Controller
{
    public function rate(Request $request, Test $test){

        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $existing = Rating::where('user_id', $request->user()->id)
        ->where('test_id', $test->id)
        ->first();

        if ($existing && $existing->stars == $request->rating) {
            return response()->json([
                'success' => true,
                'rating' => $existing->stars,
            ]);
        }

        Rating::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'test_id' => $test->id,
            ],
            [
                'stars' => $request->rating
            ]
        );

        return response()->json([
            'test' => $test->id,
            'existing' => $existing,
            'user' => $request->user()->id,
            'success' => true,
            'rating' => $request->rating
        ]);
    }
}
