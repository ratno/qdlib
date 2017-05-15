<?php

namespace QD\Lib;

use Illuminate\Support\ServiceProvider;

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
