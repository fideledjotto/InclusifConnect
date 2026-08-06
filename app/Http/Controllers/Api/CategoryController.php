<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Liste des thématiques, avec le nombre de ressources publiées.
     */
    public function index()
    {
        $categories = Category::withCount('publishedDocuments')->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        $category->loadCount('publishedDocuments');

        return new CategoryResource($category);
    }
}
