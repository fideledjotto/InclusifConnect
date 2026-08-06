<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Liste des favoris de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $documents = $request->user()->favorites()->with('category')->latest('favorites.created_at')->get();

        return DocumentResource::collection($documents);
    }

    /**
     * Ajouter une ressource aux favoris.
     */
    public function store(Request $request, Document $document)
    {
        $request->user()->favorites()->syncWithoutDetaching([$document->id]);

        return response()->json(['message' => 'Ajouté aux favoris.']);
    }

    /**
     * Retirer une ressource des favoris.
     */
    public function destroy(Request $request, Document $document)
    {
        $request->user()->favorites()->detach($document->id);

        return response()->json(['message' => 'Retiré des favoris.']);
    }
}
