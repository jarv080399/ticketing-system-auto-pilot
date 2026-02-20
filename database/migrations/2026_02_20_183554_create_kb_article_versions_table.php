<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kb_article_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('kb_articles')->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('title');
            $table->longText('content');
            $table->foreignId('changed_by')->constrained('users');
            $table->string('change_summary')->nullable();
            
            // Log tables usually only need created_at, but we'll use timestamps to comply with standard
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_article_versions');
    }
};
