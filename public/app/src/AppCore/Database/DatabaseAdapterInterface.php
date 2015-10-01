<?php

namespace AppCore\Database;

interface DatabaseAdapterInterface
{
    /**
     * @return \PDO
     */
    public function getConnection();
}

