<?php

namespace AppCore;

class Router
{
    /**
     * @var Container
     */
    private $container;

    private $map;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        // @todo place in config
        $this->map = [
            '/' => [
                'controller' => '\UrlShortener\Controller\UrlShortenerController',
                'action' => 'indexAction'
            ],
            '/([a-zA-Z0-9]{1,})' => [
                'controller' => '\UrlShortener\Controller\UrlShortenerController',
                'action' => 'redirectAction',
                'parameters' => ['code']
            ],
            '/short_url' => [
                'controller' => '\UrlShortener\Controller\UrlShortenerController',
                'action' => 'shortUrlAction',
                'method' => 'isXmlRequest' // @todo check method
            ],
        ];
    }

    /**
     * @param array $request
     * @return array
     */
    public function match($request)
    {
        $matchedParameters = [];
        $requestURI = $this->parseRequest($request);

        foreach ($this->map as $pattern => $parameters) {
            $pattern = str_replace('/', '\/', $pattern);
            if (preg_match('/^' . $pattern . '$/', $requestURI, $matches)) {
                $matchedParameters = $parameters;
                unset($matchedParameters['parameters']);

                if (!empty($parameters['parameters'])) {
                    foreach ($parameters['parameters'] as $i => $name) {
                        $matchedParameters['parameters'][$name] = (isset($matches[$i + 1])) ? $matches[$i + 1] : null;
                    }
                }
                break;
            }
        }

        if (empty($matchedParameters)) {
            $matchedParameters = $this->getDefaultParameters();
        }

        return $matchedParameters;
    }

    /**
     * @param array $request
     * @return string
     */
    private function parseRequest(array $request)
    {
        $requestURI = isset($request['path']) ? '/' . $request['path'] : '/';
        return $requestURI;
    }

    /**
     * @return array
     */
    private function getDefaultParameters()
    {
        return [
            'controller' => 'AppCore\Controller\NotFoundController',
            'action' => 'indexAction',
        ];
    }
}