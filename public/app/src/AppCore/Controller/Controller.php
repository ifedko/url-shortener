<?php

namespace AppCore\Controller;

use AppCore\Container;
use AppCore\View\View;

abstract class Controller
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/../app/src/View/';
        $this->view = new View($templatePath);
    }

    /**
     * @var string $viewPath
     * @var array $parameters
     */
    protected function render($viewPath, array $parameters = [])
    {
        return $this->view->render($viewPath, $parameters);
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param sting $url
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
}
