<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DrugSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   
    return redirect()->route('drug.search.form'); 
});

Route::get('/dashboard', function () {
   
    return redirect()->route('drug.search.form'); 
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/drug-search', [DrugSearchController::class, 'showSearchForm'])->name('drug.search.form');
    Route::post('/drug-search', [DrugSearchController::class, 'search'])->name('drug.search.submit');
});

require __DIR__.'/auth.php';
