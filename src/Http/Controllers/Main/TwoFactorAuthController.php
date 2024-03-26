<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Main;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use SaguiAi\MixpostAdapter\Actions\TwoFactorAuth\ConfirmTwoFactorAuth;
use SaguiAi\MixpostAdapter\Actions\TwoFactorAuth\DisableTwoFactorAuth;
use SaguiAi\MixpostAdapter\Actions\TwoFactorAuth\EnableTwoFactorAuth;
use SaguiAi\MixpostAdapter\Actions\TwoFactorAuth\RegenerateTwoFactorAuthRecoveryCodes;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;

class TwoFactorAuthController extends Controller
{
    use UsesAuth;

    public function enable(EnableTwoFactorAuth $enable): JsonResponse
    {
        $user = self::getAuthGuard()->user();

        if ($user->hasTwoFactorAuthEnabled()) {
            abort(403);
        }

        $enable($user);

        $user->load('twoFactorAuth');

        return response()->json([
            'svg' => $user->twoFactorQrCodeSvg(),
            'secret_key' => $user->twoFactorAuthSecretKey(),
        ]);
    }

    public function confirm(ConfirmTwoFactorAuth $confirm): JsonResponse
    {
        $user = self::getAuthGuard()->user();

        if ($user->hasTwoFactorAuthEnabled()) {
            abort(403);
        }

        $confirm($user, Request::input('code'));

        return response()->json([
            'recovery_codes' => $user->twoFactorRecoveryCodes(),
        ]);
    }

    public function showRecoveryCodes(): JsonResponse
    {
        return response()->json([
            'recovery_codes' => self::getAuthGuard()->user()->twoFactorRecoveryCodes(),
        ]);
    }

    public function regenerateRecoveryCodes(RegenerateTwoFactorAuthRecoveryCodes $regenerate): JsonResponse
    {
        $regenerate(self::getAuthGuard()->user());

        return response()->json([
            'recovery_codes' => self::getAuthGuard()->user()->twoFactorRecoveryCodes(),
        ]);
    }

    public function disable(DisableTwoFactorAuth $disable): Response
    {
        $disable(self::getAuthGuard()->user());

        return response()->noContent();
    }
}
