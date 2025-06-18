<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;
use GuzzleHttp\Client;

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

        // ce-ci détecte si c'était une nouvelle conversation
          $isNew = !$conversationId;


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

        //guzzle pour streaming

        $model = $request->input('model', 'openai/gpt-3.5-turbo');
        $client = new Client();

        $payload = [
            'model' => $model,
            'messages' => [
                // instruction
                [
                    'role' => 'system',
                    'content' => implode("\n", [
                        "Tu es Stella, une intelligence artificielle amicale. Réponds clairement avec plusieurs explications, sans fautes d'orthographe, en français.
                        Utilise du Markdown uniquement pour les exemples de code. et ajouter 1 à 3 emojis",
                    ]),
                ],
                //message user
                ['role' => 'user', 'content' => $question,],
            ],
            'max_tokens' => 500,
            'stream' => true, // activation du streaming
        ];
        $headers = [
            'Authorization' => 'Bearer ' . env  ('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'https://localhost',
            'OpenAI-Referer' => 'https://localhost',
            'Accept' => 'text/event-stream',
            'Content-Type' => 'application/json',
        ];
        return response()->stream(function () use ($client, $headers, $payload, $conversation, $isNew){
            $response = $client->post('https://openrouter.ai/api/v1/chat/completions',
            [
                'headers' => $headers,
                'json' => $payload,
                'stream' => true,
            ]);

            $body = $response->getBody();

            $text='';
            $buffer ='';
            while(!$body->eof()){
                $chunk =$body->read(1024);
                $buffer .= $chunk;
                while(($pos = strpos($buffer, "\n")) !== false){
        $line = substr($buffer, 0, $pos);
        $buffer = substr($buffer, $pos+1);
                    $line = trim($line);

                    if(str_starts_with($line, 'data:')){
                        $data = trim(substr($line, 5));
                        if($data === '[DONE]') {break 2;}

                        if($data){
                        $json = json_decode($data, true);
                        if(isset($json['choices'][0]['delta']['content']))
                        {
                            $partial = $json['choices'][0]['delta']['content'];
                               $text .= $partial;
                                echo "data: " . json_encode(['content' => $partial]) . "\n\n";;
                                @ob_flush();
                                @flush();
                        }
                    }

                }
            }
        }

        echo "\ndata: " . json_encode([
        "is_new" => $isNew,
        "conversation_id" => $conversation->id,
        ]) . "\n\n";
        @ob_flush();
        @flush();


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
            'content' => $text,
        ]);
    }, 200 , [
         'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
    ]);


}
}
