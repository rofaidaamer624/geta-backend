<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // إحنا API، مش عايزين نعمل redirect على أي Route زي login
        // لو رجعنا null لارافيل هيبعت 401 Unauthorized من غير ما ينادي route('login')
        return null;
    }
}
