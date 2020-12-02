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

6. 环境配置

```
# 微信小程序app_id
WECHAT_MINI_PROGRAM_APP_ID=
# 微信小程序授权密钥
WECHAT_MINI_PROGRAM_SECRET=

# 微信公众号应用app_id
WECHAT_OFFICIAL_PROGRAM_APP_ID=

# 微信公众号应用授权密钥
WECHAT_OFFICIAL_PROGRAM_SECRET=

# 短信服务
# 服务app_id
SMS_APP_ID=
# 服务key
SMS_APP_KEY=
# 验证码模板id
SMS_TEMPLATE_ID=
# 短信签名
SMS_APP_SIGN=
# 验证码有效期 单位秒
SMS_CODE_EXPIRES_IN=300
```

## 包结构

```$xslt
├── README.md 项目说明文档
├── CHANGELOG.md 项目说明文档
├── app 项目应用程序目录
│   ├── Console  控制台程序
│   ├── Exceptions 异常处理
│   ├── Http  web请求目录
│   │   ├── Controllers 控制器
│   │   │   ├── UCenter 用户中心控制器，直接迁移使用
│   ├── UCModels 用户中心数据模型
│   ├── Providers   服务提供者
│   │   ├── UCenterServiceProvider.php 包服务提供者
│   ├── Services 用户中心业务处理层
├── config  服务配置
│   ├── ucenter.php
├── database    数据库脚本
│   ├── factories
│   ├── migrations  数据库迁移脚本
│   └── seeds   数据库初始化脚本
├── routes  路由
│   ├── ucenter.php
├── tests   单元测试
│   ├── Feature
│   ├── TestCase.php
│   └── Unit
```

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

## 开放服务说明

### 短信服务

支持腾讯短信服务

### 用户服务

支持手机号、小程序、公众号平台用户注册、授权等操作


