<?php

namespace Vinelab\Http;

use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('vinelab.httpclient',function ($app) {
            return new Client();
        });

        $this->app->booting(function () {

            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('HttpClient', 'Vinelab\Http\Facades\Client');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
