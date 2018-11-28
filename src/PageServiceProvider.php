<?php

namespace Nocarefree\PageManager;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
        Console\UninstallCommand::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $route_config = app('config')->get('admin.route');

        Route::domain( $route_config->domain )
             ->middleware( 'web' )
             ->namespace( $this->namespace )
             ->prefix( $route_config->prefix )
             ->group( __DIR__ . '/../routes/web.php' );

        $admin_route_path = base_path('routes/admin.php');
        if(file_exists($admin_route_path)){
            Route::domain( $route_config->domain )
                 ->middleware( ['admin','web'] )
                 ->namespace( $route_config->namespace )
                 ->prefix( $route_config->prefix )
                 ->group( $admin_route_path );
        }
        
    }

    public function register()
    {
        $this->publishes([__DIR__ . '/../config/admin.php' => config_path('admin.php')], 'admin');
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
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