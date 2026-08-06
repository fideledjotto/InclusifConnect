<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        NewsletterSubscriber::firstOrCreate(['email' => $data['email']], ['subscribed_at' => now()]);

        return response()->json(['message' => "Merci ! Votre inscription à la lettre d'information est confirmée."], 201);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        NewsletterSubscriber::where('email', $data['email'])->delete();

        return response()->json(['message' => 'Désinscription confirmée.']);
    }
}
