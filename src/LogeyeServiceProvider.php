<?php

namespace Nwogu\Logeye\Laravel;

use Illuminate\Support\ServiceProvider;

class LogeyeServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(\Filebase\Database::class, function () {
            return new \Filebase\Database([
                'dir' => __DIR__ . '/database'
            ]);
        });

        $this->app->extend('log', function () {
            return new LogManager($this->app);
        });

        $this->mergeConfigFrom(
            __DIR__. "/../config/logeye.php", "logeye"
        );
    }

    public function boot()
    {
        $this->app['router']->addRoute("GET", Controller::$url, Controller::class);

        $this->publishes([
            __DIR__. "/../config/logeye.php" => config_path('logeye.php')
        ]);
    }

}