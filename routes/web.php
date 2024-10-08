<?php

use App\Http\Controllers\downloadFileController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\RekapController;
use App\Http\Middleware\AdminMiddleware;
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
    return redirect('/user');
});
//Route::get('user-dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');
//Route::get('pelatihan/{pelatihan:slug}', PelatihanController::class)->name('pelatihan.show');
//Route::get('pelatihan/{pelatihan:slug}/materi/{materi}', ViewMateriController::class)->name('materi.show');
//Route::get('pelatihan/{pelatihan:slug}/tugas/{materi}/', [TugasController::class, 'index'])->name('tugas.show');
//Route::post('mengerjakan/{materi}', [TugasController::class, 'mengerjakan'])->name('tugas.mengerjakan');
Route::middleware('auth')->group(function () {
    Route::get('download/{file}', downloadFileController::class)->name('download');
    Route::get('kuis/{kuis}', [KuisController::class, 'show'])->name('kuis.show');
    Route::post('kuis', [KuisController::class, 'store'])->name('kuis.store');
    Route::get('reviewKuis/{kuis}', [KuisController::class, 'review'])->name('kuis.review');
        Route::get('rekapModul/{modul}', [RekapController::class, 'indexModul'])->name('rekap.modul')->middleware(\App\Http\Middleware\PengajarMiddleware::class);
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('previewKuis/{kuis}', [KuisController::class, 'adminPreview'])->name('kuis.preview');
        Route::get('adminReview/{kuis}', [KuisController::class, 'adminReview'])->name('kuis.adminReview');
    });

});
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

require __DIR__ . '/auth.php';
