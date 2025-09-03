<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'message' => 'L’email est déjà utilisé par un autre utilisateur.'
                ], 409);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role ?? 'user',
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token');

            return response()->json([
                'token' => $token->plainTextToken,
                'role' => $user->role,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l’utilisateur',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Identifiants incorrects'
                ], 401); // 401 Unauthorized
            }

            $token = $user->createToken('auth_token');

            return response()->json([
                'token' => $token->plainTextToken,
                'role' => $user->role,

            ], 200); // 200 OK
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }



    public function user(Request $request)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = $request->user();

            // Vérifier si l'utilisateur existe
            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            // Retourner les informations de l'utilisateur 
            return response()->json([
                'message' => 'Données recuperées',
                'data' => $user,
            ], 200);
        } 
        
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie',
            ], 200); // 200 OK
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
}
