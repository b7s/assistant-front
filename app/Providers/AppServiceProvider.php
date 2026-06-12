<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\ApiUserProvider;
use App\Service\ApiClient;
use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

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
                apiPrefixVersion: $config->string('assistant.api_prefix_version', 'v1'),
            );
        });
    }

    public function boot(): void
    {
        $this->customAuthProvider();
        $this->configureCommands();
        $this->configureUrl();
        $this->configureDates();
        $this->configureModels();
        $this->configureVite();
        // $this->preventStrayRequests();
        $this->setDefaultPassword();
        $this->eagerLoaderRelationships();
        $this->configureScheduleCommands();
        $this->jsonResource();
    }

    private function customAuthProvider(): void
    {
        Auth::provider('api-session', static fn ($app) => $app->make(ApiUserProvider::class));

    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->environment('production'));
    }

    private function configureUrl(): void
    {
        URL::forceHttps($this->app->environment('production'));
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }

    private function eagerLoaderRelationships(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }

    /**
     * @throws Throwable
     */
    private function preventStrayRequests(): void
    {
        Http::preventStrayRequests();

        $allowedDomains = config('services.allowed_domains', []);

        if ($allowedDomains !== []) {
            $normalizedPatterns = array_map(
                static fn (string $pattern): string => str_contains($pattern, '://') ? $pattern : "*://{$pattern}",
                $allowedDomains
            );

            Http::allowStrayRequests($normalizedPatterns);
        }
    }

    private function configureVite(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }

    private function setDefaultPassword(): void
    {
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

    /**
     * Configure model observers
     * This ensures observers are always registered, even outside the tenant context
     */
    private function configureObservers(): void {}

    private function configureScheduleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app->afterResolving(Schedule::class, static function (Schedule $schedule): void {
                $schedule->command('pulse:check --once')->everyTwentySeconds();
            });
        }
    }

    private function jsonResource(): void
    {
        JsonResource::withoutWrapping();
    }
}
