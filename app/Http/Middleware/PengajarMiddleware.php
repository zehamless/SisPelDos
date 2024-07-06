<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PengajarMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'pengajar') {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
