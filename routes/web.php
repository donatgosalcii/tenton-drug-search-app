<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DrugSearchController;
use App\Http\Controllers\StoredDrugController;
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

    Route::get('/stored-drugs', [StoredDrugController::class, 'index'])->name('stored-drugs.index');
    Route::get('/stored-drugs/create', [StoredDrugController::class, 'create'])->name('stored-drugs.create');
    Route::post('/stored-drugs', [StoredDrugController::class, 'store'])->name('stored-drugs.store');
    Route::delete('/stored-drugs/{drug}', [StoredDrugController::class, 'destroy'])->name('stored-drugs.destroy');
});

require __DIR__.'/auth.php';
