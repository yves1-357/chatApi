<?php

namespace App\Http\Controllers;
use App\Models\Conversation;
use \App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function index(){// renvoie conversations avec leurs dates
        $conversations = Conversation::orderBy('updated_at', 'desc')->get();
        return response()->json($conversations);
    }

    public function destroyAll(){
        \App\Models\Message::query()->delete();
        \App\Models\Conversation::query()->delete();
        return response()->json(['ok' => true]);
    }
}
