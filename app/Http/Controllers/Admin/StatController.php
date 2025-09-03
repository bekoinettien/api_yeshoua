<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Programme;
use App\Models\Article;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class StatController extends Controller
{

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


    public function stat(Request $request)
    {
        try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }


            $totalUsers = User::count();
            $totalProgrammes = Programme::count();
            $totalArticles = Article::count();

            return response()->json([
                'message' => 'Totaux récupérés avec succès',
                'data' => [
                    'users' => $totalUsers,
                    'programmes' => $totalProgrammes,
                    'articles' => $totalArticles,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
