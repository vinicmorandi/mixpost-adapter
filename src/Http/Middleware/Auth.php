<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use SaguiAi\MixpostAdapter\Abstracts\User;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    use UsesUserModel;
    use UsesAuth;

    public function handle(Request $request, Closure $next)
    {
        AuthFacade::shouldUse(self::getAuthGuardName());

        if (!AuthFacade::check()) {
            return $this->redirect($request);
        }

        if (!Gate::allows('viewMixpost')) {
            abort(403);
        }

        // TODO: find a better way to use the custom model instance
        if (!AuthFacade::user() instanceof User) {
            $user = self::getUserClass()::make(AuthFacade::user()->only('name', 'email'))->setAttribute('id', AuthFacade::id());

            AuthFacade::setUser($user);
        }

        return $next($request);
    }

    protected function redirect(Request $request): JsonResponse|Response
    {
        if (!$request->expectsJson()) {
            $request->session()->put('url.intended', url()->current());

            return Inertia::location(route(config('mixpost.redirect_unauthorized_users_to_route')));
        }

        return response()->json('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
    }
}
