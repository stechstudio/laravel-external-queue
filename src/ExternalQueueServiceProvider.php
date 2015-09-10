<?php

namespace Kristianedlund\LaravelExternalQueue;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\ServiceProvider;
use Kristianedlund\LaravelExternalQueue\Connectors\ExternalIronConnector;
use Kristianedlund\LaravelExternalQueue\Connectors\ExternalSqsConnector;

class ExternalQueueServiceProvider extends ServiceProvider
{


    public function boot()
    {
        $manager = $this->app['queue'];
        $manager->addConnector('externalsqs', function () {
            return new ExternalSqsConnector;
        });
        $manager->addConnector('externaliron', function () {
            return new ExternalIronConnector($this->app['encrypter'], $this->app['request']);
        });

    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}
