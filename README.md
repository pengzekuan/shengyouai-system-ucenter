## 功能介绍

用户统一注册/登录功能模块

## 使用说明

```shell script
> composer require shengyouai/shengyouai-system-ucenter:0.0.2
> php artisan vendor:publish --provider="Shengyouai\App\Providers\UCenterServiceProvider"
```

```
App\Providers\RouteServiceProvider::class

public function map()
{
    ...
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
```

```
App\Http\Kernel.php
protected $middlewareGroups = [
    'ucenter' => [
            
    ]
]

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
