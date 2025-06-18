<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller{
    public function ask(Request $request)
{
    $question = $request->input('question');
    $conversationId = $request->integer('conversation_id');
    $customInstructions = $request->input('instructions', '');
    if($customInstructions === null){
        $customInstructions ='';
    }
    Log::info('custom instructions reçues :', ['instructions' => $customInstructions]);

    // crée ou récupère la conversation
    if (! $conversationId) {
        //generer un titre
        $titlePrompt = "Tu es un générateur de titre de conversation. " .
                        "À partir de cette question : « {$question} », " .
                        "Propose un titre significatif, sous la forme d`une phrase courte (1 à 4 mots),".
                        "qui décrit précisément le sujet de la question, sans te limiter à un nombre ou une date.".
                        "Exemples de bons titres : « Histoire de Google », « Fondation de Google ».";
            $titleResponse = Http::withHeaders([
                'Authorization'  => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'OpenAI-Referer' => 'https://localhost',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'       => 'openai/gpt-3.5-turbo',
                'messages'    => [
                    ['role' => 'system', 'content' => $titlePrompt],
                    ['role' => 'user',   'content' => $question],
                ],
                'max_tokens'  => 16,
                'temperature' => 0.7,
            ]);

            if ($titleResponse->successful()) {
                $payload        = $titleResponse->json();
                $generatedTitle = trim($payload['choices'][0]['message']['content'] ?? '');
            }
            // retour sur le début de la question si titre vide
            $conversationTitle = $generatedTitle !== '' ? $generatedTitle : substr($question, 0, 40);

            $conversation = Conversation::create(['title' => $conversationTitle]);
        } else {
            $conversation = Conversation::find($conversationId);
        }

    //ic sa detecte si c'est une toute nouvelle conversation
    $isNew = ! $conversationId;

    // enregistre tout de suite le message user en base
    Message::create([
        'conversation_id' => $conversation->id,
        'role'            => 'user',
        'content'         => $question,
    ]);

    // ici on prépare l’historique complet pour le contexte suivies
    $history = Message::where('conversation_id', $conversation->id)
        ->orderBy('created_at')
        ->take(10)
        ->get(['role','content'])
        ->reverse() // mettre plus ancien au recent
        ->map(fn($m) => [
            'role'    => $m->role,
            'content' => $m->content,
        ])->toArray();

    // prompt système
    $defaultSystemPrompt = implode("\n",[
            "Tu es Stella, une IA amicale et compétente.",
            "Réponds clairement avec plusieurs explications, sans fautes, en français.",
            "Utilise du Markdown pour les exemples de code.",
            "Ajoute 1 à 3 emojis pour rendre tes réponses vivantes.",
    ]);

    $systemContent = $customInstructions = !empty($customInstructions)
            ? $customInstructions
            : $defaultSystemPrompt;

        $system = [
            'role'    => 'system',
            'content' => $systemContent,
        ];

    // ici on fusionne prompt + historique + nouvelle question
    $messagesPayload = array_merge(
        [ $system ],
        $history,
        [ ['role'=>'user','content'=> $question] ]
    );

    // payload streaming
    $model   = $request->input('model', 'openai/gpt-3.5-turbo');
    $payload = [
        'model'      => $model,
        'messages'   => $messagesPayload,
        'max_tokens' => 500,
        'stream'     => true,
    ];
    $headers = [
        'Authorization'  => 'Bearer ' . env('OPENROUTER_API_KEY'),
        'Accept'         => 'text/event-stream',
        'Content-Type'   => 'application/json',
        'HTTP-Referer'   => 'https://localhost',
        'OpenAI-Referer' => 'https://localhost',
    ];

    $client = new Client();

    // retourne un stream SSE
    return response()->stream(
        function () use ($client, $headers, $payload, $conversation, $isNew) {
            $resp = $client->post(
                'https://openrouter.ai/api/v1/chat/completions',
                ['headers'=>$headers,'json'=>$payload,'stream'=>true]
            );
            $body   = $resp->getBody();
            $text   = '';
            $buffer = '';

            while (! $body->eof()) {
                $buffer .= $body->read(1024);
                while (false !== ($pos = strpos($buffer, "\n"))) {
                    $line   = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if (! str_starts_with($line, 'data:')) {
                        continue;
                    }
                    $data = trim(substr($line, 5));
                    if ($data === '[DONE]') {
                        break 2;
                    }
                    $json = json_decode($data, true);
                    $partial = $json['choices'][0]['delta']['content'] ?? null;
                    if ($partial) {
                        $text .= $partial;
                        echo "data: " . json_encode(['content'=>$partial]) . "\n\n";
                        @ob_flush(); @flush();
                    }
                }
            }

            // envoie  flag is_new pour le front
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
        200,
        [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]
    );
}

}
