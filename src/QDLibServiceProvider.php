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
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Route::class,
            ]);
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
