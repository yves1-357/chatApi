<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;


// Page « guest chat »
Route::get('/', function() {
    return Inertia::render('GuestChat');
});


// affichage princiapal via inertia

Route::get('/chat', function(){
    return Inertia::render('Chat');
});


// Page d'inscription
Route::get('/register', function () {
    return Inertia::render('Register');
});

// Page de connexion
Route::get('/login', function () {
    return Inertia::render('Login');
});

// Traitement form-inscription
Route::post('/register', [AuthController::class, 'register']);

// Traitement form-login
Route::post('/login',    [AuthController::class, 'login']);
