<?php

namespace Bancer\ParatestDatabasesFactory;

use PDO;
use PDOStatement;

class DatabasesFactory
{
    private $pdoDsn;

    private $pdoUsername;

    private $pdoPassword;

    private $pdoOptions;

    private $pdo;

    /**
     * Sets DSN for PDO. The same format as for PDO constructor. {@see https://www.php.net/manual/en/pdo.construct.php}
     *
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     * @return $this
     */
    public function setDsn($dsn)
    {
        $this->pdoDsn = $dsn;
        return $this;
    }

    /**
     * Sets PDO username. The user must have been granted the privilege to create databases.
     *
     * @param string $username The user name for the DSN string. This parameter is optional for some PDO drivers.
     * @return $this
     */
    public function setUsername($username)
    {
        $this->pdoUsername = $username;
        return $this;
    }

    /**
     * Sets PDO password.
     *
     * @param string $password The password for the DSN string. This parameter is optional for some PDO drivers.
     * @return $this
     */
    public function setPassword($password)
    {
        $this->pdoPassword = $password;
        return $this;
    }

    /**
     * Sets PDO exra options.
     *
     * @param array $options A key=>value array of driver-specific connection options.
     * @return $this
     */
    public function setOptions($options)
    {
        $this->pdoOptions = $options;
        return $this;
    }

    /**
     * Sets PDO object.
     *
     * @param \PDO $pdo Instance of PDO object.
     * @return $this
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * Gets PDO instance.
     *
     * @return \PDO
     */
    public function getPdo()
    {
        if (!isset($this->pdo)) {
            $this->pdo = new PDO($this->pdoDsn, $this->pdoUsername, $this->pdoPassword, $this->pdoOptions);
        }
        return $this->pdo;
    }

    /**
     * Creates database.
     *
     * @param string $schema Database name.
     * @return \PDOStatement|false PDOStatement object, or false on failure.
     */
    public function createDatabase($schema)
    {
        if (getenv('TEST_TOKEN') !== false) { // Using paratest
            $schema .= getenv('TEST_TOKEN');
        }
        $sql = "CREATE DATABASE IF NOT EXISTS `$schema`";
        $Connection = $this->getPdo();
        $pdoStatement = $Connection->query($sql);
        return $pdoStatement;
    }
}
