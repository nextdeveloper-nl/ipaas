<?php

namespace NextDeveloper\IPAAS;

use NextDeveloper\Commons\AbstractServiceProvider;

/**
 * Class IPaasServiceProvider
 *
 * @package NextDeveloper\Support
 */
class IPaasServiceProvider extends AbstractServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
            __DIR__.'/../config/ipaas.php' => config_path('ipaas.php'),
            ], 'config'
        );

        $this->loadViewsFrom($this->dir.'/../resources/views', 'IPAAS');

        //        $this->bootErrorHandler();
        $this->bootChannelRoutes();
        $this->bootModelBindings();
        $this->bootLogger();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->registerHelpers();
        $this->registerMiddlewares('ipaas');
        $this->registerRoutes();
        $this->registerCommands();

        $this->mergeConfigFrom(__DIR__.'/../config/ipaas.php', 'ipaas');
    }

    /**
     * @return void
     */
    public function bootLogger()
    {
        //        $monolog = Log::getMonolog();
        //        $monolog->pushProcessor(new \Monolog\Processor\WebProcessor());
        //        $monolog->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
        //        $monolog->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['ipaas'];
    }

    //    public function bootErrorHandler() {
    //        $this->app->singleton(
    //            ExceptionHandler::class,
    //            Handler::class
    //        );
    //    }

    /**
     * @return void
     */
    private function bootChannelRoutes()
    {
        if (file_exists(($file = $this->dir.'/../config/channel.routes.php'))) {
            include_once $file;
        }
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if ( ! $this->app->routesAreCached() && config('leo.allowed_routes.ipaas', true) ) {
            $this->app['router']
                ->namespace('NextDeveloper\IPAAS\Http\Controllers')
                ->group(__DIR__.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'api.routes.php');
        }
    }

    /**
     * Registers module based commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [

                ]
            );
        }
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
