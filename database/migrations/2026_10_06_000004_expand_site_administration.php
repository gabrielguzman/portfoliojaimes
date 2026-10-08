<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up(): void {
  Schema::create('site_pages', function(Blueprint $table) {
   $table->id();$table->string('key')->unique();$table->json('content');$table->text('seo_description')->nullable();$table->timestamps();
  });
  foreach(config('site_content') as $key=>$page) DB::table('site_pages')->insert(['key'=>$key,'content'=>json_encode($page['defaults']),'created_at'=>now(),'updated_at'=>now()]);
  Schema::create('disciplines',function(Blueprint $table) { $table->id();$table->string('name')->unique();$table->timestamps(); });
  $names=array_unique(array_merge(['Pintura','Dibujo','Fotografía','Arte textil','Técnica mixta','Docencia'],DB::table('projects')->distinct()->pluck('category')->all()));
  foreach($names as $name) DB::table('disciplines')->insert(['name'=>$name,'created_at'=>now(),'updated_at'=>now()]);
  Schema::table('profiles',function(Blueprint $table){
   $table->string('role')->default('Profesora de artes visuales');$table->string('brand_subtitle')->default('ARTES VISUALES & DOCENCIA');
   $table->string('footer_text')->default('Arte, aprendizaje y exploración.');
   $table->string('portrait')->nullable();$table->string('portrait_preview')->nullable();$table->string('portrait_alt')->nullable();$table->string('cv')->nullable();
  });
 }
 public function down(): void {
  Schema::dropIfExists('site_pages');Schema::dropIfExists('disciplines');
  Schema::table('profiles',fn(Blueprint $table)=>$table->dropColumn(['role','brand_subtitle','footer_text','portrait','portrait_preview','portrait_alt','cv']));
 }
};
