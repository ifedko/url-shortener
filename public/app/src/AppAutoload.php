<?php

class AppAutoload
{
    public static function autoload($class)
    {
        $class = ltrim($class, '\\');
        $file  = '';
        $namespace = '';

        if ($lastNsPos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $file  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $file .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        $file = $_SERVER['DOCUMENT_ROOT'] . '/../app/src/' . $file;

        if (is_readable($file)) {
            require $file;
        }
    }
}