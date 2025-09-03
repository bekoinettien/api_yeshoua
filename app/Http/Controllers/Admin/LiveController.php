<?php

namespace App\Http\Controllers\Admin;

use App\Models\Live;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LiveController extends Controller
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

    // Liste des lives
    public function index()
    {
        return response()->json([
            'message' => 'Liste des lives',
            'data' => Live::all()
        ], 200);
    }

    // Créer un live
    public function store(Request $request)
    {
        try{
            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }
            
             $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'startTime' => 'nullable|date',
            'endingTime' => 'nullable|date',
            'lien' => 'required|string|max:255',
        ]);
         $live= new Live();
        $live->title = $request->title;
        $live->description = $request->description;
        $live->startTime = $request->startTime;
        $live->endingTime = $request->endingTime;
        $live->lien = $request->lien;
        

        if ($request->hasFile('couverture')) {
                $path = $request->file('couverture')->store('couverturelive', 'public'); 
                $url = asset('storage/' . $path);
                $live->couverture = $url;
            }
        $live->save();
         return response()->json([
            'message' => 'Live créé avec succès',
            'data' => $live
        ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la vérification des droits d\'accès'], 403);
        }
       
       
    }

    // Afficher un live
    public function show($id)
    {
        $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

        return response()->json($live, 200);
    }

    // Modifier un live
    public function update(Request $request, $id)
    {
        $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'startTime' => 'nullable|date',
            'endingTime' => 'nullable|date',
            'lien' => 'sometimes|string|max:255',
            'viewers' => 'sometimes|integer|min:0',
        ]);

        $live->update($validated);

        return response()->json([
            'message' => 'Live mis à jour avec succès',
            'data' => $live
        ], 200);
    }

    // Supprimer un live
    public function destroy($id)
    {
        $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

        $live->delete();

        return response()->json(['message' => 'Live supprimé avec succès'], 200);
    }

    // Incrémenter les viewers
    public function incrementViewers($id)
    {
        $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

        $live->increment('viewers');

        return response()->json([
            'message' => 'Vue ajoutée',
            'viewers' => $live->viewers
        ], 200);
    }
}
