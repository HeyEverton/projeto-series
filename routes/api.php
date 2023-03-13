<?php

use App\Models\Episode;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function() {

    Route::apiResource('/series', \App\Http\Controllers\Api\SeriesController::class);

    Route::get('series/{series}/seasons', function (Series $serie) {
        return response()->json([
            $serie->seasons
        ]);
        // return $serie->seasons;
    });

    Route::get('series/{series}/episodes', function (Series $serie) {
        return $serie->episodes;
    });

    Route::patch('episodes/{episode}', function (Episode $episode, Request $request) {
        $episode->watched = $request->watched;
        $episode->save();

        return $episode;
    });
});


Route::post('login', function(Request $request) {
    $credentials = $request->only(['email', 'password']);
    $user = \App\Models\User::whereEmail($credentials['email'])->first();
    if(!Auth::attempt($credentials)) {
        return response()->json([
            'exception' => 'EmailOrPasswordIncorrect',
            'message' => 'E-mail or password is incorrect.'
        ], 403);
    }

    $user = Auth::user();
    $user->tokens()->delete();
    $token = $user->createToken('token', ['series:delete']);
    return response()->json([
        'acess_token' => $token->plainTextToken
    ]);
});
