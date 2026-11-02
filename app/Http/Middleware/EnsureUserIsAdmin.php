<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses khusus administrator dan petugas.');
        }

        $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array($role, ['admin', 'staff'], true)) {
            abort(403, 'Akses khusus administrator dan petugas.');
        }

        return $next($request);
    }
}
