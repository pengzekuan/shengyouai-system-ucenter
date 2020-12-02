<?php

namespace Shengyouai\App\Providers;

use Illuminate\Support\ServiceProvider;

class UCenterServiceProvider extends ServiceProvider
{
    public function boot()
    {

        // 配置文件迁移
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../../config/ucenter.php' => config_path('ucenter.php')
            ], 'config');
        }

        $distCPath = $this->app->basePath() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers'
            . DIRECTORY_SEPARATOR . 'UCenter';

        if (!is_dir($distCPath)) {
            mkdir($distCPath);
        }

        // 控制器迁移
        $this->publishes([
            __DIR__ . '/../Http/Controllers/UCenter/' => $this->app->basePath() . '/app/Http/Controllers/UCenter/'
        ], 'routes');


        // 控制器，路由迁移
        $this->publishes([
            __DIR__ . '/../../routes/ucenter.php' => $this->app->basePath() . '/routes/ucenter.php'
        ], 'routes');

        // 迁移脚本
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * 注册绑定
     */
    public function register()
    {

        // 合并配置
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/ucenter.php',
            'ucenter'
        );
    }
}
