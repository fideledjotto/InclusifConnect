<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Handicap moteur', 'slug' => 'moteur', 'icon' => '🦽', 'description' => 'Mobilité, aides techniques, aménagements.'],
            ['name' => 'Handicap sensoriel', 'slug' => 'sensoriel', 'icon' => '👁️', 'description' => 'Vision, audition, communication adaptée.'],
            ['name' => 'Handicap cognitif', 'slug' => 'cognitif', 'icon' => '🧠', 'description' => 'Apprentissage, troubles DYS, autisme.'],
            ['name' => 'Accessibilité numérique', 'slug' => 'numerique', 'icon' => '💻', 'description' => 'Sites web, applications, RGAA.'],
            ['name' => 'Accessibilité des bâtiments', 'slug' => 'batiments', 'icon' => '🏢', 'description' => 'ERP, voirie, normes de construction.'],
            ['name' => 'Textes de loi', 'slug' => 'loi', 'icon' => '⚖️', 'description' => 'Lois, décrets et droits des personnes.'],
            ['name' => "Outils d'apprentissage", 'slug' => 'apprentissage', 'icon' => '📚', 'description' => 'Formations, tutoriels, supports pédagogiques.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
