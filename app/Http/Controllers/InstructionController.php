<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructionController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'instructions' => $user?->instruction?->content ?? ''
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'instructions' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();

        $user->instruction()->updateOrCreate(
            ['user_id' => $user->id],
            ['content' => $data['instructions'] ?? '',
            'name' => $user->name,
            'email'=> $user->email,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->instruction()?->delete();

        return response()->json(['ok' => true]);
    }
}
