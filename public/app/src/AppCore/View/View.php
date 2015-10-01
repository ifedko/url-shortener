<?php

namespace AppCore\View;

class View
{
    const VIEW_FILE_EXTENSION = 'php';

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $templatePath
     */
    public function __construct($templatePath)
    {
        $this->path = rtrim($templatePath, '/') . '/';
    }

    /**
     * @param string $viewName
     * @param array $parameters
     */
    public function render($viewName, array $parameters = [])
    {
        $subPathElements = explode(':', $viewName);
        $subPath = implode('/', $subPathElements) . '.' . self::VIEW_FILE_EXTENSION;

        $viewPath = rtrim($this->path, '/') . '/' . $subPath;

        $parameters['pathToLayout'] = $this->path;
        extract($parameters);

        include $viewPath;
    }
}