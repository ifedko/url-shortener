<?php

namespace AppCore\Database;

use AppCore\Database\Exception\DatabaseAdapterException;

class MysqlAdapter implements DatabaseAdapterInterface
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \PDO
     */
    private $pdo = null;

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct($dsn, $username, $password, $database, $options = array())
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    /**
     * @return \PDO
     * @throws DatabaseAdapterException
     */
    protected function createConnection()
    {
        try {
            $pdo = new \PDO($this->dsn, $this->username, $this->password);
        } catch (\PDOException $exception) {
            throw new DatabaseAdapterException(sprintf('Connection failed: %s', $exception->getMessage()));
        }

        return $pdo;
    }

    /**
     * @return \PDO
     * @throws DatabaseAdapterException
     */
    public function getConnection()
    {
        if ($this->pdo == null) {
            $this->pdo = $this->createConnection();
        }

        return $this->pdo;
    }
}
