<?php

namespace SaguiAi\MixpostAdapter\Actions\Auth;

use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Support\LoginRateLimiter;

class PrepareAuthenticatedSession
{
    public function __construct(protected readonly LoginRateLimiter $limiter)
    {
    }

    public function handle(Request $request, callable $next)
    {
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->limiter->clear($request);

        return $next($request);
    }
}
