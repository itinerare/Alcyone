<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin {
    /**
     * Redirect non-admins to the home page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$request->user() || !$request->user()->isAdmin) {
            flash('You do not have the permission to access this page.')->error();

            return redirect('/');
        }

        return $next($request);
    }
}
