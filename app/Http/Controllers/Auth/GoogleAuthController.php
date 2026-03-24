<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Actions\Auth\AuthenticateWithGoogleAction;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $redirectTo = $request->get('redirect_to', 'exp://');
        $state = base64_encode(json_encode(['redirect_to' => $redirectTo]));

        return Socialite::driver('google')
            ->redirectUrl(route('google.callback'))
            ->stateless()
            ->with(['state' => $state])
            ->redirect();
    }

    public function callback(Request $request, AuthenticateWithGoogleAction $action)
    {
        try {
            $state = json_decode(base64_decode($request->get('state', '')), true);
            $redirectTo = $state['redirect_to'] ?? 'exp://';
        } catch (\Exception $e) {
            $redirectTo = 'exp://';
        }

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('google.callback'))
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect()->away($redirectTo . (str_contains($redirectTo, '?') ? '&' : '?') . 'error=google_auth_failed');
        }

        $token = $action->handle($googleUser);

        return redirect()->away($redirectTo . (str_contains($redirectTo, '?') ? '&' : '?') . 'token=' . $token);
    }
}
