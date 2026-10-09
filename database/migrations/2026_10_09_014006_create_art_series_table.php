<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('art_series', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->boolean('published')->default(false);
            $table->string('cover')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('art_series_project', function (Blueprint $table) {
            $table->foreignId('art_series_id')->constrained('art_series')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->primary(['art_series_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('art_series_project');
        Schema::dropIfExists('art_series');
    }
};
