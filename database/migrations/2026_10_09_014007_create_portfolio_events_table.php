<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->boolean('published')->default(false);
            $table->string('kind')->default('exhibition');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('location');
            $table->string('address')->nullable();
            $table->string('cover')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_events');
    }
};
