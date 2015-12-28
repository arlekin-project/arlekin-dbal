<?php

namespace Arlekin\DatabaseAbstractionLayer;

use Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager;
use Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManagerInterface;

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
     * @var array
     */
    protected $configuration;
    
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
