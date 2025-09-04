<?php

namespace App\Http\Controllers\Admin;

use App\Models\Live;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

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
    public function index(Request $request)
    {
        try{
            if($response =$this->checkAdmin($request)){
                return $response;
            }
             return response()->json([
            'message' => 'Liste des lives',
            'data' => Live::all()
        ], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
       
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
    public function show(Request $request ,$id)
    {
        try{
            if($response=$this->checkAdmin($request)){
                return $response;
            }
            $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

          return response()->json($live, 200);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    // Modifier un live
    public function update(Request $request, $id)
    {
        try{
            if($response=$this->checkAdmin($request)){
                return $response;

            }
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

        $live->save($validated);

        return response()->json([
            'message' => 'Live mis à jour avec succès',
            'data' => $live
        ], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    // Supprimer un live
    public function destroy(Request $request,$id)
    {
        try{
            if($response=$this->checkAdmin($request)){
                return $response;

            }
            $live = Live::find($id);

        if (!$live) {
            return response()->json(['message' => 'Live introuvable'], 404);
        }

        $live->delete();

        return response()->json(['message' => 'Live supprimé avec succès'], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
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

    public function list_live_for_user(Request $request)
    {
        try {
            $lives = Live::select('id', 'title', 'description', 'viewers')
            ->get();

        return response()->json([
            'message' => 'Liste des lives',
            'data' => $lives,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }
}