<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Manager;

use Arlekin\DatabaseAbstractionLayer\DriverInterface;
use Arlekin\DatabaseAbstractionLayer\Exception\DbalException;

class DatabaseConnectionManager implements DatabaseConnectionManagerInterface
{
    /**
     * @var array
     */
    protected $configuration;
    
    /**
     * @var array
     */
    protected $driversByName;
    
    /**
     * @var array
     */
    protected $connectionsByName;

    /**
     * Constructor.
     * 
     * @param array $configuration
     * @param array $driversByName
     */
    public function __construct(array &$configuration, array &$driversByName)
    {
        $this->configuration = $configuration;
        
        $this->driversByName = $driversByName;
        
        $this->connectionsByName = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function instanciateDatabaseConnection(array &$parameters)
    {
        $driverParameter = $parameters['driver'];

        if (!isset($this->driversByName[$driverParameter])) {
            throw new DbalException(
                sprintf(
                    'Found no driver with name "%s".',
                    $driverParameter
                )
            );
        }

        $driver = $this->driversByName[$driverParameter];

        return $driver->instanciateDatabaseConnection($parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function instanciateNamedDatabaseConnection($name)
    {
        if (!isset($this->configuration['connections'][$name])) {
            throw new DbalException(
                sprintf(
                    'Found no database connection with name "%s".',
                    $name
                )
            );
        }

        return $this->instanciateDatabaseConnection(
            $this->configuration['connections'][$name]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionWithName($name)
    {
        if (!isset($this->connectionsByName[$name])) {
            $connection = $this->instanciateNamedDatabaseConnection($name);

            $this->connectionsByName[$name] = $connection;
        }

        return $this->connectionsByName[$name];
    }
}
