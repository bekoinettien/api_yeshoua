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

class ArticleController extends Controller
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


    public function add_article(Request $request)
    {
        try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'category' => 'required|string|max:255',
                'contenu' => 'required|string',
                'feature_image' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
            ]);

            $article = new Article();
            $article->title = $request->title;
            $article->author = $request->author;
            $article->category = $request->category;
            $article->status = "brouillon";
            $article->contenu = $request->contenu;

          
            if ($request->hasFile('feature_image')) {
                $path = $request->file('feature_image')->store('Acover', 'public'); 
                $url = asset('storage/' . $path);
                $article->feature_image = $url;
            }

            $article->save();

            return response()->json([
                'message' => 'Article ajouté avec succès',
                'data' => $article,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function list_article(Request $request)
    {
        try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

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


    public function update_article(Request $request, $id)
    {
        try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

            
            $article = Article::find($id);

            if (!$article) {
                return response()->json([
                    'message' => 'Article non trouvé.'
                ], 404);
            }

            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'author' => 'nullable|string|max:255',
                'category' => 'sometimes|required|string|max:255',
                'contenu' => 'sometimes|required|string',
                'feature_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->has('title')) $article->title = $request->title;
            if ($request->has('author')) $article->author = $request->author;
            if ($request->has('category')) $article->category = $request->category;
            if ($request->has('status')) $article->status = $request->status;
            if ($request->has('contenu')) $article->contenu = $request->contenu;

            // Gestion de l'image
            if ($request->hasFile('feature_image')) {
                $path = $request->file('feature_image')->store('Acover', 'public');
                $article->feature_image = asset('storage/' . $path);
            }

            $article->save();

            return response()->json([
                'message' => 'Article mis à jour avec succès',
                'data' => $article,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function delete_article(Request $request, $id)
    {
        try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

            $article = Article::find($id);

            if (!$article) {
                return response()->json([
                    'message' => 'Article non trouvé.'
                ], 404);
            }

            $article->delete();

            return response()->json([
                'message' => 'Article supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
 
}