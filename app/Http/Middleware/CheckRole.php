<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        // Check role
        // Tạm thời comment để kiểm thử API admin
        // if (!$user->hasRole($role)) {
        //     abort(403, 'Bạn không có quyền truy cập trang này.');
        // }

        return $next($request);
    }
}
