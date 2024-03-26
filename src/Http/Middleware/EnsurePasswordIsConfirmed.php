<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SaguiAi\MixpostAdapter\Concerns\ConfirmsPasswords;
use Closure;

class EnsurePasswordIsConfirmed
{
    use ConfirmsPasswords;

    /**
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->passwordIsConfirmed()) {
            throw ValidationException::withMessages([
                'confirmation' => [__('mixpost::auth.confirm_your_password')],
            ]);
        }

        return $next($request);
    }
}
