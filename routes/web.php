<?php

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

Route::get('/logs/{id}/pdf', [\App\Http\Controllers\PdfController::class, 'downloadImportLog'])
    ->name('logs.pdf');

// Form Pendataan Mandiri PSE (Public Form)
Route::get('/pendataan-pse', [\App\Http\Controllers\PseSubmissionController::class, 'index'])
    ->name('pse.submit.index');
Route::post('/pendataan-pse', [\App\Http\Controllers\PseSubmissionController::class, 'store'])
    ->name('pse.submit.store');

