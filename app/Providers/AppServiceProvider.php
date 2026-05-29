<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\ApiUserProvider;
use App\Service\ApiClient;
use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiClient::class, static function (Container $app): ApiClient {
            $config = $app->make(Repository::class);

            return new ApiClient(
                apiUrl: $config->string('assistant.api_url'),
                clientId: $config->string('assistant.api_client_id'),
                clientSecret: $config->string('assistant.api_client_secret'),
                timeout: $config->integer('assistant.api_timeout'),
            );
        });
    }

    public function boot(): void
    {
        Auth::provider('api-session', static fn ($app) => $app->make(ApiUserProvider::class));

        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(static fn (): ?Password => app()->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
