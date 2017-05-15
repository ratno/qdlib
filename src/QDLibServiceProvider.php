<?php

namespace QD\Lib;

use Illuminate\Support\ServiceProvider;
use QD\Lib\Commands\Route;

class QDLibServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/qdlib.php' => config_path('qdlib.php'),
            __DIR__ . '/web/' => base_path('web'),
            __DIR__ . '/qd_app/' => base_path('app/qd'),
        ],"qdlib");

        if ($this->app->runningInConsole()) {
            $this->commands([
                Route::class,
            ]);
        } else {
            if(($user = user()) instanceof Users) {
                $role = strtolower($user->Role->Name);
            } else {
                $role = "public";
            }

            $route_override = "app/qd/routes/{$role}.php";
            $route_generated = "app/qd/routes/{$role}_generated.php";

            $this->loadRoutesFrom(base_path($route_override));
            $this->loadRoutesFrom(base_path($route_generated));
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
