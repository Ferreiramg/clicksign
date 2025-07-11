<?php

namespace Clicksign;

use Clicksign\Contracts\ClicksignClientInterface;
use Clicksign\Http\ClicksignClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ClicksignServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/clicksign.php', 'clicksign');

        $this->app->singleton(ClicksignClientInterface::class, function ($app) {
            $config = $app['config']['clicksign'];

            $baseUrl = $config['sandbox']
                ? $config['sandbox_url']
                : $config['base_url'];

            return new ClicksignClient(
                accessToken: $config['access_token'],
                baseUrl: $baseUrl
            );
        });

        $this->app->alias(ClicksignClientInterface::class, 'clicksign');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/clicksign.php' => $this->app->configPath('clicksign.php'),
            ], 'clicksign-config');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            ClicksignClientInterface::class,
            'clicksign',
        ];
    }
}
