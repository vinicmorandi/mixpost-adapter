<?php

namespace SaguiAi\MixpostAdapter\Actions\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SaguiAi\MixpostAdapter\Support\LoginRateLimiter;
use Symfony\Component\HttpFoundation\Response;

class EnsureLoginIsNotThrottled
{
    public function __construct(protected readonly LoginRateLimiter $limiter)
    {
    }

    public function handle($request, $next)
    {
        if (!$this->limiter->tooManyAttempts($request)) {
            return $next($request);
        }

        event(new Lockout($request));

        return $this->lockoutResponse($request);
    }

    protected function lockoutResponse(Request $request)
    {
        return with($this->limiter->availableIn($request), function ($seconds) {
            throw ValidationException::withMessages([
                'email' => [
                    trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        });
    }
}
