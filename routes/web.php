<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => "Bienvenue sur l'API Inclusif Connect. Voir /api/documents, /api/categories, etc.",
    ]);
});
