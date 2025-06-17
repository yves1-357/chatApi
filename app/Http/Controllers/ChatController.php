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
        $conversationId = $request->integer('conversation_id');

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

        $model = $request->input('model', 'openai/gpt-3.5-turbo');
        $reponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env  ('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'https://localhost',
            'OpenAI-Referer' => 'https://localhost',
        ])->post('https://openrouter.ai/api/v1/chat/completions',[
            'model' => $model, //modele simple pour debuter
            'messages' => [
                // instruction llm
                [
                    'role' => 'system',
                    'content' => implode("\n", [
                        "Tu es Stella, une intelligence artificielle amicale et compétente, toujours prête à répondre de façon claire, utile et bienveillante, sur tous les sujets.",
                        "Utilise du Markdown pour la mise en forme quand c'est pertinent (exemple, du code, des listes, des titres).",
                        "Ajoute 1 à 2 emojis pour rendre tes réponses vivantes.",
                        "Rédige en français, de façon claire, polie et sans fautes.",
                    ]),
                ],
                //message user
                ['role' => 'user', 'content' => $question,],
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
