<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Programme;
use App\Models\Article;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleUserController extends Controller
{
       public function list_article_for_user(Request $request)
    {
        try {

            $articles = Article::all();

            if ($articles->isEmpty()) {
                return response()->json([
                    'message' => 'Aucun article trouvé.'
                ], 404);
            }

            return response()->json([
                'message' => 'Liste des articles récupérée avec succès',
                'data' => $articles,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
