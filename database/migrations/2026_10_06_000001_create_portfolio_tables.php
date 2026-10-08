<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function(Blueprint $table) { $table->boolean('is_admin')->default(false); });
        Schema::create('projects', function(Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique(); $table->text('description');
            $table->string('category'); $table->unsignedSmallInteger('year'); $table->string('technique')->nullable();
            $table->string('dimensions')->nullable(); $table->string('cover')->nullable();
            $table->boolean('published')->default(false)->index(); $table->boolean('featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('project_images', function(Blueprint $table) {
            $table->id(); $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path'); $table->string('caption')->nullable(); $table->string('alt');
            $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('profiles', function(Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('intro'); $table->text('bio');
            $table->string('email')->nullable(); $table->string('instagram')->nullable(); $table->string('location')->nullable(); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_images'); Schema::dropIfExists('projects'); Schema::dropIfExists('profiles');
        Schema::table('users', fn(Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
