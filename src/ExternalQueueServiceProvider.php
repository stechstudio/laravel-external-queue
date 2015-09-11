<?php namespace Kristianedlund\LaravelExternalQueue;

use Illuminate\Support\ServiceProvider;
use Kristianedlund\LaravelExternalQueue\Connectors\ExternalIronConnector;
use Kristianedlund\LaravelExternalQueue\Connectors\ExternalSqsConnector;

class ExternalQueueServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Lumen doesn't support publishin
        if (substr($this->app->version(), 0, 5) != 'Lumen') {
            $this->publishes([
                __DIR__ . '/../config/externalqueue.php' => config_path('externalqueue.php')
            ], 'config');
        }

        $this->app->configure('externalqueue');

        $queueManager = $this->app['queue'];

        $queueManager->addConnector('externalsqs', function () {
            return new ExternalSqsConnector;
        });
        $queueManager->addConnector('externaliron', function () {
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
