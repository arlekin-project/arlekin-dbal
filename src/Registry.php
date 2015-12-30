<?php

namespace Arlekin\Dbal;

use Arlekin\Dbal\Manager\DatabaseConnectionManager;
use Arlekin\Dbal\Manager\DatabaseConnectionManagerInterface;

class Registry
{
    /**
     * @var DatabaseConnectionManagerInterface
     */
    protected $databaseConnectionManager;
    
    /**
     * @var array
     */
    protected $driversByName;
    
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
