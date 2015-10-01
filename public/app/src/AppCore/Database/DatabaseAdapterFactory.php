<?php

namespace AppCore\Database;

class DatabaseAdapterFactory
{
    /**
     * @param string $dbAdapterName
     * @param array $parameters
     * @return mixed
     */
    static public function create($dbAdapterName, array $parameters)
    {
        switch($dbAdapterName) {
            case 'Mysql':
            default:
                $dbAdapterClass = 'AppCore\Database\MysqlAdapter';
                $options = (!empty($parameters['options'])) ? $parameters['options'] : [];
                $dbAdapter = new $dbAdapterClass($parameters['dsn'], $parameters['username'], $parameters['password'], $options);
                break;
        }

        return $dbAdapter;
    }
}