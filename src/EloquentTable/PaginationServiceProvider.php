<?php

namespace Scuti\EloquentTable;

use Illuminate\Pagination\PaginationServiceProvider as LaravelPaginationServiceProvider;

class PaginationServiceProvider extends LaravelPaginationServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $method ="singleton";

        if (!method_exists($this->app, $method)) {
            $method = "bindShared";
        }

        $this->app->$method('paginator', function ($app) {
            $paginator = new TablePaginatorFactory($app['request'], $app['view'], $app['translator']);

            $paginator->setViewName($app['config']['view.pagination']);

            $app->refresh('request', $paginator, 'setRequest');

            return $paginator;
        });
    }
    
    public function provides()
    {
        return ['paginator'];
    }
}