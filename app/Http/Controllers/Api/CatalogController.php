<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DocumentResource;
use App\Models\Category;
use App\Models\Document;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function summary()
    {
        $totalDocuments = Document::published()->count();
        $totalCategories = Category::count();
        $featured = Document::published()
            ->with('category')
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        return response()->json([
            'stats' => [
                'documents' => $totalDocuments,
                'categories' => $totalCategories,
                'last_updated' => Document::published()->max('published_at'),
                'popular' => Document::published()->sum('views_count'),
            ],
            'featured' => DocumentResource::collection($featured),
            'top_categories' => CategoryResource::collection(
                Category::withCount(['publishedDocuments' => fn ($query) => $query->where('status', 'publie')])
                    ->orderByDesc('published_documents_count')
                    ->take(5)
                    ->get()
            ),
        ]);
    }

    public function featured(Request $request)
    {
        $query = Document::published()->with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $request->string('category')));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $documents = $query->orderByDesc('views_count')->limit($request->integer('limit', 6))->get();

        return DocumentResource::collection($documents);
    }
}
