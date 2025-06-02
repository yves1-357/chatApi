<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function ask(Request $request){
        $question = $request->input('question');
        //app openrouter
        $reponse = Http::withHeaders([
            'Authorization' => 'Bearer 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',
            'HTTP-Referer' => 'https://localhost',
            'OpenAI-Referer' => 'https://localhost',
        ])->post('https://openrouter.ai/api/v1/chat/completions',[
            'model' =>'openai/gpt-3.5-turbo', //modele simple pour debuter
            'messages' => [
                ['role' => 'user', 'content' => $question]
            ],
            'max_tokens' => 500,
        ]);

        if($reponse->successful()){
            $data = $reponse->json();
            //on prend le texte donné ou genere
            $answer = $data['choices'][0]['message']['content'] ?? "Pas de reponse.";
        } else {
            $answer = "Erreur Api :" . $reponse->body();
        }
        return response()->json([
            'answer' => $answer,
        ]);

    }
}
