<?php

use App\Http\Controllers\KuisController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ViewMateriController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//Route::get('user-dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');
//Route::get('pelatihan/{pelatihan:slug}', PelatihanController::class)->name('pelatihan.show');
//Route::get('pelatihan/{pelatihan:slug}/materi/{materi}', ViewMateriController::class)->name('materi.show');
//Route::get('pelatihan/{pelatihan:slug}/tugas/{materi}/', [TugasController::class, 'index'])->name('tugas.show');
//Route::post('mengerjakan/{materi}', [TugasController::class, 'mengerjakan'])->name('tugas.mengerjakan');

Route::get('download/{file}', \App\Http\Controllers\downloadFileController::class)->name('download');
Route::get('kuis/{kuis}', [KuisController::class, 'show'])->name('kuis.show');
Route::post('kuis', [KuisController::class, 'store'])->name('kuis.store');
Route::get('reviewKuis/{kuis}', [KuisController::class, 'review'])->name('kuis.review');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

require __DIR__.'/auth.php';
