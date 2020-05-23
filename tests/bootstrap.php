<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Request;
use Hyperf\Utils\ApplicationContext;

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
define('BASE_PATH', $dir = dirname(__DIR__, 1));

$container = new Container((new DefinitionSourceFactory(true))());
ApplicationContext::setContainer($container);
$container->define(RequestInterface::class, Request::class);

$container->define(\Psr\SimpleCache\CacheInterface::class, function () use ($container) {
    return new \Hyperf\Cache\Driver\FileSystemDriver($container, []);
});

$container->define(\Hyperf\Contract\SessionInterface::class, function () {
    return new Hyperf\Session\Session('testing', new \Hyperf\Session\Handler\FileHandler(
        new \Hyperf\Utils\Filesystem\Filesystem(),
        BASE_PATH . '/runtime/testing',
        10
    ));
});
