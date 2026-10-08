<?php
namespace App\Http\Middleware;
use Inertia\Middleware;
use Illuminate\Http\Request;
use App\Models\SitePage;
class HandleInertiaRequests extends Middleware {
 protected $rootView='app';
 public function share(Request $request): array { return [...parent::share($request),'site'=>fn()=>SitePage::publicContent()]; }
}
