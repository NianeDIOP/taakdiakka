<?php

use App\Http\Controllers\DemandeController;
use App\Models\Demande;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $demandes = Demande::latest('published_at')->take(6)->get();

    return view('welcome', compact('demandes'));
})->name('home');

Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
