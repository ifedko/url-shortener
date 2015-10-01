<?php

namespace AppCore;

use AppCore\Controller;
use AppCore\Controller\ControllerFactory;
use AppCore\Database\DatabaseAdapterFactory;
use UrlShortener\Manager\ShortUrlManager;

class Application
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Router
     */
    protected $router;

    public function __construct()
    {
        $this->config = new Config();
        $this->container = new Container();
    }

    /**
     * @return $this
     */
    public function build()
    {
        $config = $this->config;

        $dbParameters = $config->getSectionParameters('database');
        $dbAdapter = DatabaseAdapterFactory::create('Mysql', $dbParameters);

        $shortUrlManager = new ShortUrlManager($dbAdapter, $config);

        $this->container->set('short_url_manager', $shortUrlManager);
        $this->router = new Router($this->container);

        return $this;
    }

    /**
     * @param array $request
     */
    public function run(array $request)
    {
        $controllerParameters = $this->router->match($request);

        $controllerName = $controllerParameters['controller'];
        $action = $controllerParameters['action'];
        $parameters = !empty($controllerParameters['parameters']) ? $controllerParameters['parameters'] : [];
        $request = array_merge($request, $parameters);

        $controllerFactory = new ControllerFactory($this->container);
        $controller = $controllerFactory->create($controllerName, $action);
        $controller->$action($request);
    }
}