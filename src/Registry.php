<?php

namespace Calam\Dbal;

use Calam\Dbal\Manager\DatabaseConnectionManager;
use Calam\Dbal\Manager\DatabaseConnectionManagerInterface;

class Registry
{
    /**
     * @var DatabaseConnectionManagerInterface
     */
    private $databaseConnectionManager;

    /**
     * @var array
     */
    private $driversByName;

    /**
     * @param array $configuration
     */
    public function __construct(array &$configuration = [])
    {
        $this->driversByName = [];

        $this->databaseConnectionManager = new DatabaseConnectionManager($configuration, $this->driversByName);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getConnectionWithName($name)
    {
        return $this->databaseConnectionManager->getConnectionWithName($name);
    }

    /**
     * @param string $name
     * @param DriverInterface $driver
     */
    public function registerDriver($name, DriverInterface $driver)
    {
        $this->driversByName[$name] = $driver;
    }
}
