<?php namespace wneuteboom\SphinxSearch;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * AWS SDK for PHP service provider for Laravel applications
 */
class SphinxSearchServiceProvider extends ServiceProvider
{
    const VERSION = '1.0.0';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the configuration
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/../config/sphinxsearch.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('sphinxsearch.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sphinxsearch');
        }

        $this->mergeConfigFrom($source, 'sphinxsearch');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sphinxsearch', function ($app) {
            $config = $app->make('config')->get('sphinxsearch');

            return new SphinxSearch($config);
        });

        $this->app->alias('sphinxsearch');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sphinxsearch'];
    }

}
