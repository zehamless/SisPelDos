<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PengajarMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(auth()->user()->role, ['pengajar', 'admin'])) {
            return redirect()->route('filament.user.pages.dashboard');
        }
        return $next($request);
    }
}
