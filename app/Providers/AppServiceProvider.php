<?php

namespace App\Providers;

use App\Search\ElasticSearchEngine;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // LOADING MIGRATIONS FROM SUB-FOLDERS
        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*' , GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);
        $this->loadMigrationsFrom($paths);

        // ENABLE REDIS WATCHERS
        Redis::enableEvents();

        // ELASTICSEARCH ENGINE
        $this->app->singleton('elasticsearch', function () {
            return ClientBuilder::create()
               ->setHosts([
                    '127.0.0.1:9200'
                ])
                ->build();
        });

        resolve(EngineManager::class)->extend('elasticsearch', function () {
            return new ElasticSearchEngine(
                app('elasticsearch')
            );
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
