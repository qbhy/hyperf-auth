# qbhy/hyperf-auth
hyperf 的 auth 组件，目前支持 jwt、session 驱动。用户可以自行扩展。  
本组件参考了 laravel 的 auth 组件设计，使用体验大体和 laravel 的 auth 差不多。

## 安装 - install
```bash
$ composer require 96qbhy/hyperf-auth
```

## 配置 - configuration
使用 `Qbhy\HyperfAuth\AuthExceptionHandler`
```php
<?php
// config/autoload/exceptions.php
return [
    'handler' => [
        'http' => [
            \Qbhy\HyperfAuth\AuthExceptionHandler::class,
        ],    
    ],
];
```

发布配置 vendor:publish
```bash
$ php bin/hyperf.php vendor:puhlish 96qbhy/hyperf-auth
```
修改 `config/autoload/auth.php`
> 如不需要自定义 guard、model 和 user provider，则可以不修改
```php
<?php

use Doctrine\Common\Cache\FilesystemCache;
use Qbhy\SimpleJwt\Encoders\Base64UrlSafeEncoder;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;

return [
    'default' => [
        'guard'=> 'jwt',
        'provider'=> 'users',
    ] ,
    'guards' => [ // 开发者可以在这里添加自己的 guard ，guard Qbhy\HyperfAuth\AuahGuard 接口
        'jwt' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'users',
            'secret' => new PasswordHashEncrypter(env('JWT_SECRET', 'qbhy/hyperf-auth')),
            'encoder' => new Base64UrlSafeEncoder(),
            'cache' => new FilesystemCache(sys_get_temp_dir()), // 如果需要分布式部署，请选择 redis 或者其他支持分布式的缓存驱动
        ],
        'session' => [
            'driver' => Qbhy\HyperfAuth\Guard\SessionGuard::class,
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => \Qbhy\HyperfAuth\Provider\EloquentProvider::class, // user provider 需要实现 Qbhy\HyperfAuth\UserProvider 接口
            'model' => App\Model\User::class, //  需要实现 Qbhy\HyperfAuth\Authenticatable 接口
        ],
    ],
];
```

## 使用 - usage
> 以下是伪代码，仅供参考。Auth 注解可以用于类或者方法。
```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Qbhy\HyperfAuth\Annotation\Auth;use Qbhy\HyperfAuth\AuthManager;

/**
* @Controller
* Class IndexController
*/
class IndexController extends AbstractController
{
  /**
   * @Inject
   * @var AuthManager
   */
  protected $auth;

  /**
   * @GetMapping(path="/login")
   * @return array
   */
  public function login()
  {
      /** @var User $user */
      $user = User::query()->firstOrCreate(['name' => 'test', 'avatar' => 'avatar']);
      return [
          'status' => $this->auth->guard('session')->login($user),
      ];
  }

  /**
   * @Auth("session")
   * @GetMapping(path="/logout")
   */
  public function logout()
  {
      $this->auth->guard('session')->logout();
      return 'logout ok';
  }

  /**
   * 使用 Auth 注解可以保证该方法必须通过某个 guard 的授权，支持同时传多个 guard，不传参数使用默认 guard
   * @Auth("session")
   * @GetMapping(path="/user")
   * @return string
   */
  public function user()
  {
      $user = $this->auth->guard('session')->user();
      return 'hello '.$user->name;
  }
}
```

## 更多用法 - API
```php
<?php

$auth = auth(); // 控制器内也可以通过 @Inject 注入

$user = new \HyperfTest\DemoUser(1);

// 直接调用 AuthGuard 方法，这种情况下会获取 默认 guard 然后调用 guard 的对应方法
$auth->login($user); // 默认使用 jwt 驱动，该方法将返回 token 字符串
$auth->logout(); // 退出登录
$auth->check(); // 检查是否登录
$auth->guest(); // 是否游客/是否未登录
$auth->user(); // 若登录返回当前登录用户，否则返回null

/** @var \Qbhy\HyperfAuth\Guard\JwtGuard $jwtGuard */
$jwtGuard = $auth->guard('jwt');
$jwtGuard->user('your token or null'); // jwt 驱动支持手动传入 token，如不传或者传null则从 request 中解析
$jwtGuard->check('your token or null');
$jwtGuard->guest('your token or null');


$auth->guard()->login($user); // guard 方法不传参数或者传null都将使用默认值

// 使用 session 驱动需要安装 hyperf/session 并启用 session
$auth->guard('session')->login($user); // guard 方法不传参数或者传null都会获取默认值
// 
```

## 扩展 - extension
由于本组件参考了 laravel auth 的设计，所以 guard 和 user provider 的扩展也和 laravel 类似。只需要实现对应接口即可。
* guard ===> Qbhy\HyperfAuth\AuthGuard  
* user provider ===> Qbhy\HyperfAuth\UserProvider  

https://github.com/qbhy/hyperf-auth  
qbhy0715@qq.com  