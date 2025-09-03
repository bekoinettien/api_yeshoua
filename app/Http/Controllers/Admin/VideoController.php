<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
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

    public function add_video(Request $request)
    {
         try {

            if ($response = $this->checkAdmin($request)) {
                return $response; 
            }

            $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'couverture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'video_url' => 'nullable|file|mimes:mp4,mov,avi,mkv|max:20480', 
            'status' => 'in:published,draft',
            'duration' => 'required|string',
            'views'  => 'nullable|integer',
            ]);

            $video = new Video();
            $video->programme_id = $request->programme_id;
            $video->title = $request->title;
            $video->description = $request->description;
            $video->couverture = $request->couverture;
            $video->video_url = $request->video_url;
            $video->status = $request->status;
            $video->duration = $request->duration;
            $video->views = $request->views ?? 0;


            if ($request->hasFile('couverture')) {
                $path = $request->file('couverture')->store('couvertures', 'public'); 
                $url = asset('storage/' . $path);
                $video->couverture = $url;
            }
            
            if ($request->hasFile('video_url')) {
                $path = $request->file('video_url')->store('videos', 'public');
                $url = asset('storage/' . $path);
                $video->video_url = $url;
            }
            
            $video->save();

            return response()->json([
                'message' => 'Vidéo ajoutée avec succès',
                'data' => $video,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function list_videos(Request $request)
    {
        try {
            if ($response = $this->checkAdmin($request)) {
                return $response;
            }

            $videos = Video::all();

            return response()->json([
                'message' => 'Liste des vidéos',
                'data' => $videos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_video(Request $request, $id)
    {
        try {
            if ($response = $this->checkAdmin($request)) {
                return $response;
            }

            $video = Video::find($id);
            if (!$video) {
                return response()->json(['message' => 'Vidéo introuvable'], 404);
            }

            $request->validate([
                'programme_id' => 'sometimes|exists:programmes,id',
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'couverture' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'video_url' => 'sometimes|file|mimes:mp4,mov,avi,mkv|max:20480',
                'status' => 'sometimes|in:published,draft',
                'duration' => 'sometimes|string',
                'views' => 'sometimes|integer',
            ]);

            if ($request->has('programme_id')) {
                $video->programme_id = $request->programme_id;
            }
            if ($request->has('title')) {
                $video->title = $request->title;
            }
            if ($request->has('description')) {
                $video->description = $request->description;
            }
            if ($request->hasFile('couverture')) {
                $path = $request->file('couverture')->store('couvertures', 'public');
                $url = asset('storage/' . $path);
                $video->couverture = $url;
            }
            if ($request->hasFile('video_url')) {
                $path = $request->file('video_url')->store('videos', 'public');
                $url = asset('storage/' . $path);
                $video->video_url = $url;
            }
            if ($request->has('status')) {
                $video->status = $request->status;
            }
            if ($request->has('duration')) {
                $video->duration = $request->duration;
            }
            if ($request->has('views')) {
                $video->views = $request->views;
            }

            $video->save();

            return response()->json([
                'message' => 'Vidéo mise à jour avec succès',
                'data' => $video,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
}

    public function delete_video(Request $request, $id)
    {
        try {
            if ($response = $this->checkAdmin($request)) {
                return $response;
            }

            $video = Video::find($id);
            if (!$video) {
                return response()->json(['message' => 'Vidéo introuvable'], 404);
            }

            $video->delete();

            return response()->json([
                'message' => 'Vidéo supprimée avec succès',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function list_videos_for_user(Request $request)
    {
        try {
            
            $videos = Video::where('status', 'published')->get();

            return response()->json([
                'message' => 'Liste des vidéos',
                'data' => $videos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}