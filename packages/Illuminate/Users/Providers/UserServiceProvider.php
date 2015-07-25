<?php

namespace PhpSoft\Illuminate\Users\Providers;

use Illuminate\Support\ServiceProvider;
use PhpSoft\Illuminate\Users\Commands\MigrationCommand;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../config/jwt.php' => config_path('jwt.php'),
            __DIR__ . '/../config/entrust.php' => config_path('entrust.php'),
        ]);

        // Register commands
        $this->commands('phpsoft.command.migration');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->bindShared('phpsoft.command.migration', function ($app) {
            return new MigrationCommand();
        });
    }
}