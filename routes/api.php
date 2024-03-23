<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('get-dosen-data', \App\Http\Controllers\GetDosenDataController::class)->name('get-dosen-data');
Route::get('list-dosen', \App\Http\Controllers\getListDosenController::class)->name('get-list-dosen');
