<?php

use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminDocumentController;
use App\Http\Controllers\Api\Admin\AdminStatsController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NewsletterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (visiteurs, sans compte)
|--------------------------------------------------------------------------
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);

Route::get('/catalog/summary', [CatalogController::class, 'summary']);
Route::get('/catalog/featured', [CatalogController::class, 'featured']);

Route::get('/documents', [DocumentController::class, 'index']);
Route::get('/documents/{slug}', [DocumentController::class, 'show']);
Route::post('/documents/{document}/download', [DocumentController::class, 'download']);

Route::post('/newsletter/subscribe', [NewsletterController::class, 'store']);
Route::delete('/newsletter/unsubscribe', [NewsletterController::class, 'destroy']);

Route::post('/contact', [ContactController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Routes authentifiées (visiteur connecté)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{document}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{document}', [FavoriteController::class, 'destroy']);

    // Un visiteur connecté propose un document (publication soumise à validation admin)
    Route::post('/documents', [DocumentController::class, 'store']);

    /*
    |----------------------------------------------------------------------
    | Espace administrateur
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminStatsController::class, 'index']);
        Route::get('/activity-log', [AdminStatsController::class, 'activityLog']);

        Route::apiResource('categories', AdminCategoryController::class)->except(['show']);

        Route::get('/documents', [AdminDocumentController::class, 'index']);
        Route::post('/documents', [AdminDocumentController::class, 'store']);
        Route::put('/documents/{document}', [AdminDocumentController::class, 'update']);
        Route::delete('/documents/{document}', [AdminDocumentController::class, 'destroy']);
        Route::post('/documents/{document}/approve', [AdminDocumentController::class, 'approve']);
        Route::post('/documents/{document}/reject', [AdminDocumentController::class, 'reject']);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
    });
});
