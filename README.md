## 功能介绍

用户统一注册/登录功能模块

## 使用说明

1. 安装依赖

```shell script
> composer require shengyouai/shengyouai-system-ucenter:0.0.8
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

### 用户登录/注册接口

接口地址：`/ucenter/oauth`

公共参数
```json5
{
  "p": "0|1|2", // 授权平台 0 手机号 1 小程序 2 公众号
  "cId": "", // 用户来源渠道id，小程序、公众号、App的appId，或者系统自定义渠道id，如用户分享
  "ct": "", // 来源类型 0 分享用户；1 小程序应用；2 公众号；3 三方平台 -1 其他 
  "scene": "" // 来源场景值
}
```

- 手机号注册/登录

    - 方法：`post`
    - 参数类型：`application/json`
    - 参数：
        ```json5
        {
          "cellphone": "手机号",
          "code": "短信验证码",
          "p": 0
        }
        ```
      
- 小程序授权登录

    - 方法：`post`
    - 参数类型：`application/json`
    - 参数：
        ```json
        {
          "code": "小程序授权code",
          "p": 1
        }
        ```
      
- 公众号网页授权

    1. 授权跳转

        - 方法：`get`
        - 参数：
            - `p` 授权平台指定 2表示公众号
            - `r` 获取授权链接还是直接跳转
            - `t` 授权回调地址，该地址必须urlencode，授权成功回调地址返回token参数，客户端直接获取保存
            ```
            p=2&r=1&t=redirect_uri
            ```
    2. 授权回调【接口端，无需调用】
   

### 退出登录接口

接口地址：`/ucenter/logout`

接口方法：`POST`

### 发送短信验证码

接口地址：`/ucenter/sms/send`

接口方法：`post`

接口参数

```json
{
  "cellphone": ""
}
```

### 短信验证

接口地址：`/ucenter/sms/verify`

接口方法：`post`

接口参数

```json
{
  "cellphone": "",
  "code": ""
}
```
