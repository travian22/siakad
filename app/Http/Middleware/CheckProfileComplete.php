<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($request->routeIs('dosen.profil*', 'mahasiswa.profil*')) {
            return $next($request);
        }

        if (! $user->isProfileComplete()) {
            $targetRoute = match ($user->role) {
                'dosen' => 'dosen.profil',
                'mahasiswa' => 'mahasiswa.profil',
                default => 'login',
            };

            return redirect()
                ->route($targetRoute)
                ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu sebelum mengakses menu lainnya.');
        }

        return $next($request);
    }
}
