<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;

// echange de données par exemple front-end pose question / conversations.etc..

Route::post('/chat', [ChatController::class, 'ask']);
Route::get('/conversations', [ConversationController::class, 'index']);
Route::get('/conversations/{id}/messages', [MessageController::class, 'index']);
Route::delete('/conversations', [ConversationController::class, 'destroyAll']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

