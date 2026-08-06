<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'administrateur')->first();

        $docs = [
            ['title' => "Guide pratique de l'accessibilité numérique RGAA", 'type' => 'Guide', 'cat' => 'numerique', 'author' => 'Équipe Inclusif Connect', 'kw' => ['RGAA', 'web', 'normes']],
            ['title' => 'Comprendre les troubles DYS en milieu scolaire', 'type' => 'Document', 'cat' => 'cognitif', 'author' => 'C. Amoussou', 'kw' => ['dys', 'école', 'cognitif']],
            ['title' => 'Vidéo : se déplacer en fauteuil roulant en ville', 'type' => 'Vidéo', 'cat' => 'moteur', 'author' => 'Réseau Mobilité+', 'kw' => ['mobilité', 'fauteuil', 'ville']],
            ['title' => 'Initiation à la langue des signes (podcast)', 'type' => 'Audio', 'cat' => 'sensoriel', 'author' => 'Association Signes & Voix', 'kw' => ['LSF', 'audition']],
            ['title' => "Loi sur l'accessibilité des ERP : ce qu'il faut savoir", 'type' => 'Document', 'cat' => 'loi', 'author' => 'Ministère des Solidarités', 'kw' => ['loi', 'ERP', 'droit']],
            ['title' => 'Aménager un bâtiment accessible : guide technique', 'type' => 'Guide', 'cat' => 'batiments', 'author' => 'Ordre des architectes', 'kw' => ['bâtiment', 'normes', 'travaux']],
            ['title' => 'Créer des documents accessibles avec Word', 'type' => 'Guide', 'cat' => 'numerique', 'author' => 'Équipe Inclusif Connect', 'kw' => ['bureautique', 'word', 'numérique']],
            ['title' => 'Vidéo pédagogique : le braille au quotidien', 'type' => 'Vidéo', 'cat' => 'sensoriel', 'author' => 'Institut Louis Braille', 'kw' => ['braille', 'vision']],
            ['title' => "Outils numériques pour l'apprentissage inclusif", 'type' => 'Document', 'cat' => 'apprentissage', 'author' => 'F. Konaté', 'kw' => ['éducation', 'outils', 'école']],
            ['title' => 'Podcast : témoignages sur le handicap invisible', 'type' => 'Audio', 'cat' => 'cognitif', 'author' => 'Radio Inclusion', 'kw' => ['témoignage', 'invisible']],
            ['title' => "Vos droits face à l'employeur : synthèse juridique", 'type' => 'Document', 'cat' => 'loi', 'author' => 'Défenseur des droits', 'kw' => ['emploi', 'droit', 'loi']],
            ['title' => 'Aides techniques pour la mobilité réduite', 'type' => 'Guide', 'cat' => 'moteur', 'author' => 'CNSA', 'kw' => ['aides', 'mobilité']],
        ];

        foreach ($docs as $d) {
            $category = Category::where('slug', $d['cat'])->first();

            Document::firstOrCreate(
                ['title' => $d['title']],
                [
                    'description' => "Ressource sur le thème « {$category->name} », proposée par {$d['author']}.",
                    'type' => $d['type'],
                    'category_id' => $category->id,
                    'author' => $d['author'],
                    'external_url' => 'https://example.org/ressources/'.str()->slug($d['title']),
                    'keywords' => $d['kw'],
                    'status' => 'publie',
                    'views_count' => rand(20, 130),
                    'downloads_count' => rand(5, 60),
                    'reviewed_by' => $admin?->id,
                    'published_at' => now()->subDays(rand(1, 200)),
                ]
            );
        }
    }
}
