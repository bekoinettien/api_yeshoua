<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Programme;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ProgrammeController extends Controller
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


    public function list_programmes(Request $request)
    {
        if ($response = $this->checkAdmin($request)) {
            return $response; 
        }

        $programmes = Programme::orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'message' => 'Données récupérées avec succès',
            'data' => $programmes
        ], 200);
    }

    

     public function list_programmes_for_user(Request $request)
    {
        $programmes = Programme::orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'message' => 'Données récupérées avec succès',
            'data' => $programmes
        ], 200);
    }

    

    public function add_programme(Request $request)
    {
        if ($response = $this->checkAdmin($request)) {
            return $response; 
        }

        // Validation
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'starting' => 'required|string',
            'ending' => 'required|string',
            'when' => 'required|string',
            'genre' => 'required|string',
            'couverture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4098',
        ]);

        // Upload de l'image dans le dossier Pcover
        $couverturePath = $request->file('couverture')->store('Pcover', 'public');
        $couvertureUrl = asset('storage/' . $couverturePath);

      
        $programme = Programme::create(array_merge(
            $request->except('couverture'),
            [
                'couverture' => $couvertureUrl
            ]
        ));

        return response()->json([
            'message' => 'Programme créé avec succès',
            'data' => $programme
        ], 201);
    }



    // Afficher un programme
    // public function afficher_détalis_programme($id)
    // {
    //     $programme = Programme::find($id);
    //     if (!$programme) {
    //         return response()->json(['message' => 'Programme introuvable'], 404);
    //     }
    //     return response()->json($programme, 200);
    // }




    public function update_programme(Request $request, $id)
{
   
    if ($response = $this->checkAdmin($request)) {
        return $response;
    }

    // Cherche le programme
    $programme = Programme::find($id);
    if (!$programme) {
        return response()->json(['message' => 'Programme introuvable'], 404);
    }

 
    $request->validate([
        'nom' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'starting' => 'sometimes|string',
        'ending' => 'sometimes|string',
        'when' => 'sometimes|string',
        'genre' => 'sometimes|string',
        'couverture' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
    ]);




    // Gestion de l'image si fournie
    if ($request->hasFile('couverture')) {
        // Upload nouvelle image
        $path = $request->file('couverture')->store('Pcover', 'public');
        $request->merge(['couverture' => asset('storage/' . $path)]);
    }


    // Mise à jour des champs autorisés
    $programme->update($request->only([
        'nom', 'description', 'starting', 'ending', 'when', 'genre', 'couverture'
    ]));

    $programme->refresh();

    // Retour JSON
    return response()->json([
        'message' => 'Programme mis à jour avec succès',
        'data' => $programme
    ], 200);
}



    // Supprimer
    public function delete_programme(Request $request, $id)
    {
           // Vérifie le rôle admin
    if ($response = $this->checkAdmin($request)) {
        return $response;
    }

        $programme = Programme::find($id);
        if (!$programme) {
            return response()->json(['message' => 'Programme introuvable'], 404);
        }

        $programme->delete();
        return response()->json(['message' => 'Programme supprimé'], 200);
    }


}
