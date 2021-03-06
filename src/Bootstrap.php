<?php
namespace Brave\PingApp;

use Psr\Container\ContainerInterface;

/**
 *
 */
class Bootstrap
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Bootstrap constructor
     */
    public function __construct()
    {
        $container = new \Slim\Container(require_once(ROOT_DIR . '/config/container.php'));
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Slim\App
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function enableRoutes()
    {
        /** @var \Slim\App $app */
        $routesConfigurator = require_once(ROOT_DIR . '/config/routes.php');
        $app = $routesConfigurator($this->container);

        $app->add(new \Tkhamez\Slim\RoleAuth\SecureRouteMiddleware(include ROOT_DIR . '/config/security.php'));
        $app->add(new \Tkhamez\Slim\RoleAuth\RoleMiddleware($this->container->get(RoleProvider::class)));

        $app->add(new \Slim\Middleware\Session([
            'name' => 'brave_service',
            'autorefresh' => true,
            'lifetime' => '1 hour'
        ]));

        return $app;
    }
}
