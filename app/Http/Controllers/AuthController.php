<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // inscription
    public function register(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Inscription réussie'], 201);
    }


    //connexion
    public function login (Request $request){
        $validated = $request->validate([
           'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

         $user = User::where('email', $validated['email'])->first();

         if (! $user) {
            return response()->json([
                'message' => 'E-mail incorrect, veuillez vous inscrire.'
            ], 422);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect, veuillez réessayer.'
            ], 422);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user'    => ['id' => $user->id, 'name' => $user->name]
        ]);
    }
}

