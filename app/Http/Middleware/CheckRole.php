<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silahkan login terlebih dahulu.');
        }

        // Get user role from database
        $user = Auth::user();

        // Get the role ID from the user's role relation
        $roleId = $user->roleuser->id_roleuser;

        // Check if user has any of the allowed roles
        $allowedRoles = collect($roles)->map(function($role) {
            switch(strtolower($role)) {
                case 'admin': return 1;
                case 'guru': return 2;
                case 'bk': return 3;
                default: return null;
            }
        })->filter()->toArray();

        if (in_array($roleId, $allowedRoles)) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
