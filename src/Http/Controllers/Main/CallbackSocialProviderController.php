<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Actions\Account\UpdateOrCreateAccount;
use SaguiAi\MixpostAdapter\Facades\SocialProviderManager;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class CallbackSocialProviderController extends Controller
{
    public function __invoke(Request $request, UpdateOrCreateAccount $updateOrCreateAccount, string $providerName): RedirectResponse
    {
        $provider = SocialProviderManager::connect($providerName);

        if (empty($provider->getCallbackResponse())) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid]);
        }

        if ($error = $request->get('error')) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('error', $error);
        }

        if (!$provider->isOnlyUserAccount()) {
            return redirect()->route('mixpost.accounts.entities.index', ['workspace' => WorkspaceManager::current()->uuid, 'provider' => $providerName])
                ->with('mixpost_callback_response', $provider->getCallbackResponse());
        }

        $accessToken = $provider->requestAccessToken($provider->getCallbackResponse());

        if ($error = Arr::get($accessToken, 'error')) {
            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('error', $error);
        }

        $provider->setAccessToken($accessToken);

        $account = $provider->getAccount();

        if ($account->hasError()) {
            $message = $account->hasExceededRateLimit() ? $account->message : __('mixpost::error.try_again');

            return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid])
                ->with('error', $message);
        }

        $updateOrCreateAccount($providerName, $account->context(), $accessToken);

        return redirect()->route('mixpost.accounts.index', ['workspace' => WorkspaceManager::current()->uuid]);
    }
}
