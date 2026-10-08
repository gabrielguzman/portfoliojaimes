<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
 public function up(): void {
  Schema::table('projects',fn(Blueprint $table)=>$table->string('section')->default('art')->index());
  DB::table('projects')->where('category','Docencia')->update(['section'=>'teaching']);
 }
 public function down(): void { Schema::table('projects',fn(Blueprint $table)=>$table->dropColumn('section')); }
};
