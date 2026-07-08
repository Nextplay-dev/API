<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateWithAppleAction;
use App\Http\Controllers\Controller;
use App\Services\AppleTokenService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function redirect(Request $request, AppleTokenService $appleTokenService)
    {
        $redirectTo = $request->get('redirect_to', 'exp://');
        if (! str_starts_with($redirectTo, 'exp://') && ! in_array($redirectTo, config('auth.oauth.redirect_urls'))) {
            return abort(400, 'Invalid redirect url');
        }

        $originReferrer = $request->header('X-Origin-Referrer')
            ?? $request->input('origin_referrer')
            ?? $request->header('referer');

        $state = base64_encode(json_encode([
            'redirect_to' => $redirectTo,
            'origin_referrer' => $originReferrer,
        ]));

        config(['services.apple.client_secret' => $appleTokenService->generate()]);

        return Socialite::driver('apple')
            ->redirectUrl(route('apple.callback'))
            ->stateless()
            ->with(['state' => $state])
            ->redirect();
    }

    public function callback(Request $request, AppleTokenService $appleTokenService, AuthenticateWithAppleAction $action)
    {
        try {
            $state = json_decode(base64_decode($request->get('state', '')), true);
            $redirectTo = $state['redirect_to'] ?? 'exp://';
            $originReferrer = $state['origin_referrer'] ?? null;
        } catch (\Exception $e) {
            $redirectTo = 'exp://';
            $originReferrer = null;
        }

        try {
            config(['services.apple.client_secret' => $appleTokenService->generate()]);

            $driver = Socialite::driver('apple')
                ->redirectUrl(route('apple.callback'))
                ->stateless();

            $appleUser = $driver->user();
        } catch (\Exception $e) {
            return redirect()->away($redirectTo.(str_contains($redirectTo, '?') ? '&' : '?').'error=apple_auth_failed');
        }

        try {
            $token = $action->handle($appleUser, $originReferrer);
        } catch (\Exception $e) {
            return redirect()->away($redirectTo.(str_contains($redirectTo, '?') ? '&' : '?').'error=auth_internal_error');
        }

        return redirect()->away($redirectTo.(str_contains($redirectTo, '?') ? '&' : '?').'token='.$token);
    }
}
