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

            // 数据库迁移脚本
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }


        // 控制器，路由迁移
        $this->loadRoutesFrom(__DIR__ . '/../../routes/ucenter.php');

        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }
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
