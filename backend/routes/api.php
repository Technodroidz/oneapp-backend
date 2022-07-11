<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [LoginController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('allusers',[HomeController::class, 'getAllUsers'])->name('allusers');
Route::get('fmchannels', [HomeController::class, 'fmchannels']);
Route::get('youtubechannels', [HomeController::class, 'youtubeChannels']);

Route::get('/agora-chat/{id}', [HomeController::class, 'index']);
Route::post('/agora/token', [HomeController::class, 'token']);
Route::post('/agora/call-user', [HomeController::class, 'callUser']);

Route::group(['middleware' => 'api'],function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});