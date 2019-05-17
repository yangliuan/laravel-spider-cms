<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as EsClientBuilder;

class EsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('es', function () {
            // 从配置文件读取 Elasticsearch 服务器列表
            $builder = EsClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));
            // 如果是开发环境
            // if (app()->environment() === 'local') {
            //     // 配置日志，Elasticsearch 的请求和返回数据将打印到日志文件中，方便我们调试
            //     $builder->setLogger(app('Log')->driver());
            // }

            return $builder->build();
        });
    }
}
