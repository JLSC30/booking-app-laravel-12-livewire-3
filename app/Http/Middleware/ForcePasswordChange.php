<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle($request, Closure $next)
    {
        if (
            Auth::check()
            && Auth::user()->must_change_password
            && ! $request->routeIs('password.force', 'logout')
            && ! $request->is('livewire/*')
        ) {
            return redirect()->route('password.force');
        }

        if (
            Auth::check()
            && ! Auth::user()->must_change_password
            && $request->routeIs('password.force')
        ) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
