<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('projects',fn(Blueprint $table)=>$table->string('cover_preview')->nullable());
  Schema::table('project_images',fn(Blueprint $table)=>$table->string('preview_path')->nullable());
 }
 public function down(): void {
  Schema::table('projects',fn(Blueprint $table)=>$table->dropColumn('cover_preview'));
  Schema::table('project_images',fn(Blueprint $table)=>$table->dropColumn('preview_path'));
 }
};
