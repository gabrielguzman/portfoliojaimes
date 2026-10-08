<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PortfolioController;
use App\Models\Profile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/proyectos/{slug}', [PortfolioController::class, 'show'])->name('projects.show');

Route::get('/vista-previa/{project}', [PortfolioController::class, 'preview'])->name('projects.preview');

Route::get('/obra', [PortfolioController::class, 'artwork'])->name('artwork');
Route::get('/docencia', [PortfolioController::class, 'teaching'])->name('teaching');
Route::get('/sobre-mi', [PortfolioController::class, 'about'])->name('about');
Route::get('/contacto', [PortfolioController::class, 'contact'])->name('contact');

Route::get('/curriculum', function () {
    $profile = Profile::first();
    abort_unless($profile?->cv && Storage::disk('public')->exists($profile->cv), 404);

    return Storage::disk('public')->download($profile->cv, 'curriculum.pdf', ['Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff']);
})->name('curriculum');

Route::post('/contacto', [ContactMessageController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');
