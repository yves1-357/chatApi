<?php

namespace App\Http\Controllers;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use \App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function index(){// renvoie conversations du user connecte
        $conversations = Auth::user()
        ->conversations()
        ->orderBy('updated_at', 'desc')
        ->get();
        return response()->json($conversations);
    }

    public function destroyAll(){
        Auth::user()
        ->conversations()
        ->each(function($conv){
            $conv->delete();
        });

        return response()->json(['ok' => true]);
    }
}
