<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($id){


    //recuperer message de conversation
    $messages = Message::where('conversation_id', $id)->orderBy('created_at')->get();
    return response()->json($messages);
}
}
