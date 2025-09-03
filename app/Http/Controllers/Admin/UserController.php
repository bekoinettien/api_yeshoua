<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{

    // Fonction pour vérifier le rôle admin
    private function checkAdmin(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'user') {
            // Supprimer le token actuel
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Accès refusé. Vous serez redirigé vers la page d’accueil.',
            ], 403);
        }

        else if ($user->role === 'admin')  {
            return null;
        }
    }
    

    public function list_users(Request $request)
    {
        try {
            // Vérifie le rôle admin
            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

            // Récupérer tous les utilisateurs
            $users = User::all();

            // Vérifier si des utilisateurs existent
            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'Aucun utilisateur trouvé.'
                ], 404);
            }

            // Retourner la liste des utilisateurs
            return response()->json([
                'message' => 'Liste des utilisateurs récupérée avec succès',
                'data' => $users,
            ], 200);
        } 
        
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
