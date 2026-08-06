<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Liste publique des ressources : recherche, filtres, tri, pagination.
     * Query params : q, category (slug), type, sort=date|popularite|type, per_page
     */
    public function index(Request $request)
    {
        $query = Document::query()->published()->with('category');

        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->lower().'%';
            $query->where(function ($sub) use ($term) {
                $sub->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(author) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(JSON_UNQUOTE(keywords)) LIKE ?', [$term]);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $request->string('category')));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        match ($request->string('sort')->toString()) {
            'popularite' => $query->orderByDesc('views_count'),
            'type' => $query->orderBy('type'),
            default => $query->orderByDesc('published_at'),
        };

        $documents = $query->paginate($request->integer('per_page', 12));

        return DocumentResource::collection($documents);
    }

    /**
     * Fiche détaillée d'une ressource (incrémente le compteur de vues).
     */
    public function show(Request $request, string $slug)
    {
        $document = Document::published()->with('category')->where('slug', $slug)->firstOrFail();
        $document->increment('views_count');

        return new DocumentResource($document);
    }

    /**
     * Un visiteur connecté propose un document (statut "en_attente" jusqu'à validation admin).
     */
    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document = Document::create([
            ...$data,
            'status' => 'en_attente',
            'submitted_by' => $request->user()?->id,
        ]);

        ActivityLog::record($request->user()?->id, 'document.propose', 'Document', $document->id, "Proposition : {$document->title}");

        return response()->json([
            'message' => 'Merci ! Votre document a été envoyé et sera vérifié par notre équipe avant publication.',
            'document' => new DocumentResource($document),
        ], 201);
    }

    /**
     * Enregistre un téléchargement et renvoie le fichier / lien.
     */
    public function download(Document $document)
    {
        if ($document->status !== 'publie') {
            abort(404);
        }

        $document->increment('downloads_count');

        if ($document->file_path) {
            return Storage::disk('public')->download($document->file_path, $document->title);
        }

        return response()->json(['url' => $document->external_url]);
    }
}
