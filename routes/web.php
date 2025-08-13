<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------

*/

// Pages publiques (inscription / connexion)
Route::get('/', function () {
    return Inertia::render('GuestChat');
})->name('guest.chat');

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


// tout ce qui suit nécessite d'être connecté
Route::middleware('auth')->group(function () {

    // page principale du chat
    Route::get('/chat', function () {
        return Inertia::render('Chat');
    })->name('chat');

    // endpoints AJAX pour le chat et l'historique
    Route::post('/chat', [ChatController::class, 'ask'])->name('chat.ask');
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{id}/messages', [MessageController::class, 'index'])
         ->name('conversations.messages');
    Route::delete('/conversations', [ConversationController::class, 'destroyAll'])
         ->name('conversations.destroy');

    // déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //instructions
    Route::get('/me/instructions', [InstructionController::class, 'show']);
    Route::put('/me/instructions', [InstructionController::class, 'update']);
    Route::delete('/me/instructions', [InstructionController::class, 'destroy']);
});
