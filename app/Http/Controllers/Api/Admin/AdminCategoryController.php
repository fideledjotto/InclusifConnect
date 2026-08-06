<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::withCount('documents')->orderBy('name')->get());
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::create($data);
        ActivityLog::record($request->user()->id, 'category.create', 'Category', $category->id, "Création de la thématique {$category->name}");

        return new CategoryResource($category);
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        ActivityLog::record($request->user()->id, 'category.update', 'Category', $category->id, "Modification de la thématique {$category->name}");

        return new CategoryResource($category);
    }

    public function destroy(StoreCategoryRequest $request, Category $category)
    {
        $name = $category->name;
        $category->delete();
        ActivityLog::record($request->user()->id, 'category.delete', 'Category', null, "Suppression de la thématique {$name}");

        return response()->json(['message' => 'Thématique supprimée.']);
    }
}
