<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\NewsletterSubscriber;
use App\Models\User;

class AdminStatsController extends Controller
{
    /**
     * Tableau de bord admin : vue d'ensemble du site.
     */
    public function index()
    {
        return response()->json([
            'documents' => [
                'total' => Document::count(),
                'publies' => Document::where('status', 'publie')->count(),
                'en_attente' => Document::where('status', 'en_attente')->count(),
                'refuses' => Document::where('status', 'refuse')->count(),
            ],
            'utilisateurs' => [
                'total' => User::count(),
                'administrateurs' => User::where('role', 'administrateur')->count(),
            ],
            'newsletter_abonnes' => NewsletterSubscriber::count(),
            'messages_contact_non_traites' => ContactMessage::where('is_resolved', false)->count(),
            'documents_les_plus_vus' => Document::published()->orderByDesc('views_count')->limit(5)->get(['id', 'title', 'views_count']),
            'documents_les_plus_telecharges' => Document::published()->orderByDesc('downloads_count')->limit(5)->get(['id', 'title', 'downloads_count']),
        ]);
    }

    /**
     * Historique des actions administrateur (journal d'activité).
     */
    public function activityLog()
    {
        return ActivityLog::with('user:id,name')->latest()->paginate(30);
    }
}
