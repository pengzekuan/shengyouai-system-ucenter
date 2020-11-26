## 功能介绍

用户统一注册/登录功能模块

## 使用说明

```shell script
> composer require shengyouai/shengyouai-system-ucenter:0.0.2
> php artisan vendor:publish --provider="Shengyouai\\App\\Providers\\UCenterServiceProvider"
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
