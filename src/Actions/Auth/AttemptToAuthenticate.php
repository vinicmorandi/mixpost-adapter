<?php

namespace SaguiAi\MixpostAdapter\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Support\LoginRateLimiter;

class AttemptToAuthenticate
{
    use UsesAuth;

    public function __construct(protected readonly LoginRateLimiter $limiter)
    {
    }

    public function handle(Request $request, callable $next)
    {
        if (!self::getAuthGuard()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $this->throwFailedAuthenticationException($request);
        }

        return $next($request);
    }

    protected function throwFailedAuthenticationException($request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }
}
