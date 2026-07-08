<?php

namespace App\Providers;

use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingValidatorService;
use App\Services\SlotService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use SocialiteProviders\Apple\AppleExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AvailabilityService::class);
        $this->app->bind(BookingValidatorService::class);
        $this->app->bind(SlotService::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::before(fn (User $user, string $ability) => $user->hasPermissionTo($ability) ?: null);

        $this->app->bind(Configuration::class, fn () => Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::file(storage_path('app/private/apple.p8')),
        ));

        Event::listen(
            SocialiteWasCalled::class,
            AppleExtendSocialite::class.'@handle'
        );
    }
}
