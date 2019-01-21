<?php

namespace Nocarefree\Systematics;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;


class SystematicsServiceProvider extends BaseServiceProvider
{

    protected $commands = [
        Console\InstallCommand::class,
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
    }


    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/systematics.php', 'systematics');
        $this->app->singleton('Systematics\Systematics', function () {
            return new Systematics(DB::connection());
        });
    }   


}