<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Actions\Account\UpdateOrCreateAccount;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\MetaProvider;

class AccountsController extends Controller
{
    use UsesSocialProviderManager;

    public function index(): Response
    {
        return Inertia::render('Workspace/Accounts/Accounts', [
            'accounts' => AccountResource::collection(Account::latest()->get())->resolve(),
            'is_configured_service' => Arr::except(Services::isConfigured(), ['unsplash', 'tenor']),
            'additionally' => [
                'linkedin_supports_pages' => LinkedinProvider::hasCommunityManagementProduct(),
                'meta_app_version'=> MetaProvider::getApiVersionConfig()
            ]
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = Account::firstOrFailByUuid($request->route('account'));

        if ($account->isUnauthorized()) {
            return redirect()->back()->with('error', __('mixpost::account.account_reauthenticate'));
        }

        $connection = $this->connectProvider($account);

        $response = $connection->getAccount();

        if ($response->hasError()) {
            if ($response->isUnauthorized()) {
                $account->setUnauthorized();

                return redirect()->back()->with('error', __('mixpost::account.account_reauthenticate'));
            }

            if ($response->hasExceededRateLimit()) {
                return redirect()->back()->with('error', $response->message);
            }

            return redirect()->back()->with('error', __('mixpost::account.account_not_updated'));
        }

        (new UpdateOrCreateAccount())($account->provider, $response->context(), $account->access_token->toArray());

        return redirect()->back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $account = Account::firstOrFailByUuid($request->route('account'));

        $connection = $this->connectProvider($account);

        if (method_exists($connection, 'revokeToken')) {
            $connection->revokeToken();
        }

        $account->delete();

        return redirect()->back();
    }
}
