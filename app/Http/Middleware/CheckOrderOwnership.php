<?php

namespace App\Http\Middleware;

use App\Models\BookingTransaction;
use Closure;
use Illuminate\Http\Request;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOrderOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $hasButtonClicked = $request->input('20?2310/0712?178dineInGuest') === '1';

        if (!$hasButtonClicked) {
            // Redirect the user to a 404 page
            abort(404);
        }

        return $next($request);
    }

}
