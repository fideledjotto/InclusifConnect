<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    /**
     * Tous les documents (tous statuts confondus), filtrables par ?status=en_attente
     */
    public function index(Request $request)
    {
        $query = Document::query()->with(['category', 'submitter']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return DocumentResource::collection($query->latest()->paginate(20));
    }

    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document = Document::create([
            ...$data,
            'status' => 'publie',
            'published_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        ActivityLog::record($request->user()->id, 'document.create', 'Document', $document->id, "Création directe : {$document->title}");

        return new DocumentResource($document);
    }

    public function update(StoreDocumentRequest $request, Document $document)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document->update($data);
        ActivityLog::record($request->user()->id, 'document.update', 'Document', $document->id, "Modification : {$document->title}");

        return new DocumentResource($document);
    }

    public function destroy(Request $request, Document $document)
    {
        $title = $document->title;
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        ActivityLog::record($request->user()->id, 'document.delete', 'Document', null, "Suppression : {$title}");

        return response()->json(['message' => 'Document supprimé.']);
    }

    /**
     * Valider un document proposé par un visiteur.
     */
    public function approve(Request $request, Document $document)
    {
        $document->update([
            'status' => 'publie',
            'published_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        ActivityLog::record($request->user()->id, 'document.approve', 'Document', $document->id, "Validation : {$document->title}");

        return new DocumentResource($document);
    }

    /**
     * Refuser un document proposé.
     */
    public function reject(Request $request, Document $document)
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $document->update([
            'status' => 'refuse',
            'reviewed_by' => $request->user()->id,
        ]);

        ActivityLog::record($request->user()->id, 'document.reject', 'Document', $document->id, "Refus : {$document->title}. Motif : ".$request->input('reason', '—'));

        return new DocumentResource($document);
    }
}
