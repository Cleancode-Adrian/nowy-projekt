<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Blokuje dostęp do panelu administracyjnego wszystkim poza zatwierdzonymi adminami.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        if (!$user->isAdmin() || !$user->is_approved) {
            Log::warning('Odrzucona próba dostępu do panelu admina', [
                'user_id' => $user->id,
                'role' => $user->role,
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);

            abort(403, 'Brak dostępu.');
        }

        return $next($request);
    }
}
