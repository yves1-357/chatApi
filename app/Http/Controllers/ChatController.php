<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller{

    // fonction pour envoie question
    public function ask(Request $request)
{
    // recuperation de la question tapé + id.conversation + instructions si personnalisés
    $question = $request->input('question');
    $conversationId = $request->integer('conversation_id');
    $customInstructions = $request->input('instructions', '');
    if($customInstructions === null){
        $customInstructions ='';
    }

    // enregistre les instructions recues dans le log (facile si y'a des bugs)
    Log::info('custom instructions reçues :', ['instructions' => $customInstructions]);

    $generatedTitle = ''; //stocke le titre qui pourrait etre génére

    // si Idconversation vide = nouvelle chat
    if (! $conversationId) {

        //prompt/instruction speciale pour l'Ia pour titre si c'est new chat
        $titlePrompt = "Tu es un générateur de titre de conversation. " .
                        "À partir de cette question : « {$question} », " .
                        "Propose un titre significatif, sous la forme d`une phrase courte (1 à 4 mots),".
                        "qui décrit précisément le sujet de la question, sans te limiter à un nombre ou une date.".
                        "Exemples de bons titres : « Histoire de Google », « Fondation de Google ».";
            $titleResponse = Http::withHeaders([
                'Authorization'  => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'OpenAI-Referer' => 'https://localhost',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'       => 'openai/gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => $titlePrompt],
                    ['role' => 'user',   'content' => $question],
                ],
                'max_tokens'  => 16,
                'temperature' => 0.7,
            ]);

            // recupere le texte envoyé par l'Ia
            if ($titleResponse->successful()) {
                $payload        = $titleResponse->json();
                $generatedTitle = trim($payload['choices'][0]['message']['content'] ?? '');

                // nettoie tous les guillemets  + espaces invisibles
                $generatedTitle = preg_replace('/[\"\'“”‘’«»]/u', '', $generatedTitle);

            }
            // Si titre généré on utilise or mets la question(user) 40 premier lettre + enregsitrer dans bd et si ça existe on reprend
            $conversationTitle = $generatedTitle !== '' ? $generatedTitle : substr($question, 0, 40);

            $conversation = Conversation::create(['title' => $conversationTitle]);
        } else {
            $conversation = Conversation::find($conversationId);
        }

    //note si c'est une toute nouvelle conversation ou pas
    $isNew = ! $conversationId;

    // enregistre tout de suite le question user en base
    Message::create([
        'conversation_id' => $conversation->id,
        'role'            => 'user',
        'content'         => $question,
    ]);

    // recupere 10 derniers message dans conversation pour envoie Ia ça l'aide a suivre les conversations
    $history = Message::where('conversation_id', $conversation->id)
        ->orderBy('created_at')
        ->take(10)
        ->get(['role','content'])
        ->reverse() // mettre plus ancien au recent
        ->map(fn($m) => [ // transforme en tableau simple
            'role'    => $m->role,
            'content' => $m->content,
        ])->toArray(); // convertit collection tableau en php


    // prompt système **instructions de base**
    $defaultSystemPrompt = implode("\n",[
            "Tu es Stella, une IA amicale et compétente.",
            "Réponds clairement avec plusieurs explications, sans fautes, en français.",
            "Utilise du Markdown pour les exemples de code.",
            "Ajoute 1 à 3 emojis pour rendre tes réponses vivantes.",
    ]);


    // si user ecrit instructions personnalisé on utilise sinon prends ceux de base
    $systemContent = $customInstructions = !empty($customInstructions)
            ? $customInstructions
            : $defaultSystemPrompt;

        $system = [ // crée objet pour instructions choisie
            'role'    => 'system',
            'content' => $systemContent,
        ];

    // ici on fusionne prompt + historique + nouvelle question ( paquet complet que l'Ia recoit et generer une reponse)
    $messagesPayload = array_merge(
        [ $system ],
        $history,
        [ ['role'=>'user','content'=> $question] ]
    );

    // model par default si rien n'est précisé
    $model   = $request->input('model', 'openai/gpt-4o-mini');

    $payload = [ // preparation d'info envoyer a l'iA pour génére reponse
        'model'      => $model,
        'messages'   => $messagesPayload, // contenu construit (systeme + historique)
        'max_tokens' => 1000,
        'stream'     => true, // reponse en stream/flux
    ];

    //info d'identification pour openRouter
    $headers = [
        'Authorization'  => 'Bearer ' . env('OPENROUTER_API_KEY'),
        'Accept'         => 'text/event-stream',
        'Content-Type'   => 'application/json',
        'HTTP-Referer'   => 'https://localhost',
        'OpenAI-Referer' => 'https://localhost',
    ];

    $client = new Client(); // client guzzle envoie requette http (streaming)


    // retourne une reponse un stream SSE
    return response()->stream(
        function () use ($client, $headers, $payload, $conversation, $isNew) {

            // envoie requete post pour headers et autre
            try {
            $resp = $client->post(
                'https://openrouter.ai/api/v1/chat/completions',
                ['headers'=>$headers,'json'=>$payload,'stream'=>true]
            );

            // si erreur modele + log message + prends backup modele + relance
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                    Log::warning('Modèle échoué : ' . $payload['model'] . ' -> Tentative avec modèle de secours.: google/gemini-pro-1.5');
                    $payload['model'] = 'google/gemini-pro-1.5';
                    $resp = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                        'headers' => $headers,
                        'json'    => $payload,
                        'stream'  => true
                    ]);
                }
            $body   = $resp->getBody();
            $text   = ''; // reponse iA + enregistre dans BD
            $buffer = ''; // stocke les petites morceaux jusqu'a ligne complete

            // tant qu'il y'a les données on continue a lire jusqu'a fin
            while (! $body->eof()) {
                $buffer .= $body->read(1024);
                while (false !== ($pos = strpos($buffer, "\n"))) {
                    $line   = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    //ignore les lignes qui commencent avec 'data'
                    if (! str_starts_with($line, 'data:')) {
                        continue;
                    }
                    $data = trim(substr($line, 5));
                    if ($data === '[DONE]') {
                        break 2; // si ligne contient 'Done' l'ia a finit
                    }

                    //transforme en json pour belle lecture
                    $json = json_decode($data, true);
                    $partial = $json['choices'][0]['delta']['content'] ?? null;

                    //envoi navigateur cote client
                    if ($partial) {
                        $text .= $partial;
                        echo "data: " . json_encode(['content'=>$partial]) . "\n\n";
                        @ob_flush(); @flush();// poussé données
                    }
                }
            }

            // signal pour savoir si c'est new conversation/id + mets a jour sidebar
            echo "data: " . json_encode([
                'is_new'          => $isNew,
                'conversation_id' => $conversation->id,
            ]) . "\n\n";
            @ob_flush(); @flush();

            // stocke la réponse complète en base
            Message::create([
                'conversation_id' => $conversation->id,
                'role'            => 'assistant',
                'content'         => $text,
            ]);
        },
        // fin réponse SSE + reponse arrive au flux
        200,
        [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]
    );
}

}
