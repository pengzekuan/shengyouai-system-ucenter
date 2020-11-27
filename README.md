## 功能介绍

用户统一注册/登录功能模块

## 使用说明

1. 安装依赖

```shell script
> composer require shengyouai/shengyouai-system-ucenter:0.0.2
```

2. 执行命令，引用必要文件

```shell script
> php artisan vendor:publish --provider="Shengyouai\App\Providers\UCenterServiceProvider"
```

3. 修改文件 App\Providers\RouteServiceProvider::class

```php
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
    public function map()
    {
        // ...
        //
        $this->mapUCenterRoutes();
    }
    
    protected function mapUCenterRoutes()
    {
        Route::prefix('ucenter')
            ->middleware('ucenter')
            ->namespace('Shengyouai\App\Http\Controllers\UCenter')
            ->group(base_path('routes/ucenter.php'));
    }
}
```

4. 修改文件 App\Http\Kernel.php
```php
class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        // ...
        'ucenter' => [
                
        ]
    ];
}
```

5. 修改 composer.json，加入引入文件命名空间映射

```json
{
  "autoload": {
          "psr-4": {
              "App\\": "app/",
              "Shengyouai\\App\\Http\\Controllers\\UCenter\\": "app/Http/Controllers/UCenter/"
          }
      }
}
```

## 包结构

## 接口说明

### 用户注册接口

- `/ucenter/registry`
- `POST`

### 用户登录接口

- `/ucenter/login`
- `POST`

### 用户三方授权接口

- `/ucenter/thirtyLogin`
- `POST`

### 用户退出登录接口

- `/ucenter/logout`
- `POST`
