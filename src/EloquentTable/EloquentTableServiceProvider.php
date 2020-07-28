<?php

namespace Scuti\EloquentTable;

use Illuminate\Support\ServiceProvider;

class EloquentTableServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        if (method_exists($this, 'package')) {
            $this->package('scuti/eloquenttable');
        } else {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('eloquenttable.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../views' => base_path('resources/views/scuti/eloquenttable'),
            ], 'views');

            $this->loadViewsFrom(__DIR__.'/../views', 'eloquenttable');
        }

        include __DIR__.'/../helpers.php';
    }

    public function register()
    {
    }

    public function provides()
    {
        return array('eloquenttable');
    }
}