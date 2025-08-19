<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($id)
    {
        $user = Auth::user();

        // Vérifie que la conversation appartient à l'utilisateur
        if (!$user->conversations()->where('id', $id)->exists()) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // On ne sélectionne que les champs nécessaires
        $messages = Message::where('conversation_id', $id)
            ->select('id', 'role', 'content', 'created_at')
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }
}
