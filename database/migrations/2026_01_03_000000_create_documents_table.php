<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['Document', 'Vidéo', 'Audio', 'Guide']);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('author')->nullable();
            $table->string('file_path')->nullable();   // fichier stocké sur le disque "public"
            $table->string('external_url')->nullable(); // ou lien externe (ex. vidéo hébergée ailleurs)
            $table->json('keywords')->nullable();
            $table->enum('status', ['en_attente', 'publie', 'refuse'])->default('en_attente');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'category_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
