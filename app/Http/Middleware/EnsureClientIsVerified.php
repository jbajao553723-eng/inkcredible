<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'client') {
            abort(403);
        }

        if (! $request->user()->isClientVerified()) {
            return redirect()->route('profile.verification.edit')
                ->with('error', 'Complete account verification and wait for admin approval before requesting a loan.');
        }

        return $next($request);
    }
}
