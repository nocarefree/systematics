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
        Route::middleware('web')
             ->namespace($this->namespace)->prefix(app('config')->get('admin.route','admin'))
             ->group( __DIR__ . '/../routes/web.php' );
    }

    public function register()
    {
        $this->publishes([__DIR__ . '/../config/admin.php' => config_path('admin.php')], 'admin');
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