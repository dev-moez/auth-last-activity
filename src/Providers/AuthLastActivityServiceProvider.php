<?php

namespace DevMoez\AuthLastActivity\Providers;

use Illuminate\Support\ServiceProvider;
use DevMoez\AuthLastActivity\Console\Commands\InstallAuthLastActivityCommand;

class AuthLastActivityServiceProvider extends ServiceProvider
{
    const MIGRATION_PATH = __DIR__.'/../../database/migrations/';
    const CONFIG_PATH = __DIR__.'/../../config/auth-last-activity.php';

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([self::CONFIG_PATH => config_path('auth-last-activity.php')], 'auth-last-activity-config');
     
        $this->publishes([ self::MIGRATION_PATH => database_path('migrations')], 'auth-last-activity-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands(
                commands: [
                    InstallAuthLastActivityCommand::class,
                ],
            );
        }
    }
}
