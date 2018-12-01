<?php

namespace Nocarefree\Systematics;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Nocarefree\PageManager\Http\Controllers';

    protected $routeMiddleware = [
        'admin'  => \Nocarefree\PageManager\Http\Middleware\Authenticate::class,
        'admin.guest' => \Nocarefree\PageManager\Http\Middleware\RedirectIfAuthenticated::class,
    ];

    protected $commands = [
        Console\InstallCommand::class,
        Console\UninstallCommand::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__ . '/../config/systematics.php' => config_path('systematics.php')], 'config');
        $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'migrations');

        if($this->app->runningInConsole()){
            $this->commands($this->commands);
        }

        parent::boot();
    }


    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/systematics.php', 'systematics');
    }   


}