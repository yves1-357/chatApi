<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;

class ChatController extends Controller
{
    public function ask(Request $request){
        $question = $request->input('question');
        $conversationId = $request->input('conversation_id');

        //on cree une nouvel conversation si pas  conversation_id

        if(!$conversationId){
            $title = substr($question, 0, 40); // titre::1ere caractere du question
            $conversation = Conversation::create(['title' => $title]);
        } else {
            $conversation = Conversation::find($conversationId);
        }

        //enregistrer msg vide
        if(empty($question)){
            $question ="message vide";
        }

        //enregistrer msg user
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' =>  $question,
        ]);

        //app openrouter à activer plus tard ======

        $reponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env  ('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'https://localhost',
            'OpenAI-Referer' => 'https://localhost',
        ])->post('https://openrouter.ai/api/v1/chat/completions',[
            'model' =>'openai/gpt-3.5-turbo', //modele simple pour debuter
            'messages' => [
                // instruction llm a voir plutard
                [
                    'role' => 'system',
                    'content' => "Tu es Stella, un assistant amical et concis. réponds clairement en evitant les fautes. utilise un ton poli et écoute bien la question."
                ],

                //message user
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


        /*
        $answer = "Réponse automatique stella.";

        //:jamais enregistrer un msg vide(securite)
        if (empty($answer)) {
            $answer = "Pas de réponse.";
        }

        */
        //enregistrer reponse ia
         Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $answer,
        ]);


        return response()->json([
            'answer' => $answer,
            'conversation_id' => $conversation->id,
        ]);

    }
}
