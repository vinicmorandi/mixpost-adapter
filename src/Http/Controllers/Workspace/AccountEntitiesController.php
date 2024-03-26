<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Facades\SocialProviderManager;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Http\Requests\Workspace\StoreProviderEntities;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class AccountEntitiesController extends Controller
{
    public function index(Request $request): RedirectResponse|Response
    {
        $providerName = $request->route('provider');

        if (!$request->session()->has('mixpost_callback_response')) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid]);
        }

        $provider = SocialProviderManager::connect($providerName);

        $accessToken = $provider->requestAccessToken($request->session()->get('mixpost_callback_response'));

        if ($error = Arr::get($accessToken, 'error')) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('error', $error);
        }

        $provider->setAccessToken($accessToken);

        /** @var SocialProviderResponse $response * */
        $response = $provider->getEntities();

        if ($response->hasError()) {
            $message = $response->hasExceededRateLimit() ? $response->message : __('mixpost::error.try_again');

            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('warning', $message);
        }

        $existingAccounts = Account::select('provider', 'provider_id')->get();

        $entities = collect($response->context())->map(function ($entity) use ($providerName, $existingAccounts) {
            $entity['connected'] = !!$existingAccounts
                ->where('provider', $providerName)
                ->where('provider_id', $entity['id'])
                ->first();

            return $entity;
        })->sort(function ($account) {
            return $account['connected'];
        })->values();

        if (empty($entities)) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('warning', __('mixpost::account.account_no_entities'));
        }

        return Inertia::render('Workspace/Accounts/AccountEntities', [
            'provider' => $providerName,
            'entities' => $entities
        ]);
    }

    public function store(StoreProviderEntities $storeAccountEntities): RedirectResponse
    {
        $storeAccountEntities->handle();

        return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid]);
    }
}
