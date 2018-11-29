<?php

namespace Nocarefree\PageManager;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    protected $namespace = 'Nocarefree\PageManager\Http\Controllers';

    protected $routeMiddleware = [
        'admin'  => \Nocarefree\PageManager\Http\Middleware\Authenticate::class,
        'admin.guest' => \Nocarefree\PageManager\Http\Middleware\RedirectIfAuthenticated::class,
    ];

    protected $commands = [
        Console\AdminCommand::class,
        Console\CreateUserCommand::class,
        Console\InstallCommand::class,
        Console\PublicshCommand::class,
        Console\UninstallCommand::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadRoutesFormMap();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin');

        $this->publishes([__DIR__ . '/../config/admin.php' => config_path('admin.php')], 'config');
        $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'migrations');
        $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/admin')], 'views');
        $this->publishes([__DIR__ . '/../resources/public' => public_path('vendor/admin')], 'public');


        if($this->app->runningInConsole()){
            $this->commands($this->commands);
        }

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function loadRoutesFormMap()
    {
        $route_config = app('config')->get('admin.route');

        Route::domain( $route_config['domain'] )
             ->middleware( 'web' )
             ->namespace( $this->namespace )
             ->prefix( $route_config['prefix'] )
             ->group( __DIR__ . '/../routes/web.php' );

        $admin_route_path = base_path('routes/admin.php');
        
        if(file_exists( $admin_route_path )){
            Route::domain( $route_config['domain'] )
                 ->middleware( ['admin','web'] )
                 ->namespace( $route_config['namespace'] )
                 ->prefix( $route_config['prefix'] )
                 ->group( $admin_route_path );
        }
        
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'admin');
        $this->registerRouteMiddleware();
    }   

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }
}