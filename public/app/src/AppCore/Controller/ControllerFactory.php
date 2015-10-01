<?php

namespace AppCore\Controller;

use AppCore\Container;
use AppCore\Controller\Exception\ControllerFactoryException;

class ControllerFactory
{
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
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return Controller
     */
    public function create($className, $methodName = 'indexAction')
    {
        try {
            if (!class_exists($className)) {
                throw new ControllerFactoryException(sprintf('Method %s for controller %s not found', $methodName, $className));
            }

            $controller = new $className($this->container);

            if (!method_exists($controller, $methodName)) {
                throw new ControllerFactoryException(sprintf('Method %s for controller %s not found', $methodName, $className));
            }
        } catch (ControllerFactoryException $exception) {
            $controller = $this->createNotFoundController();
        }

        return $controller;
    }

    /**
     * @return Controller
     */
    private function createNotFoundController()
    {
        return new NotFoundController($this->container);
    }
}
