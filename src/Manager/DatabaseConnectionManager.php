<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer\Manager;

use Arlekin\DatabaseAbstractionLayer\DriverInterface;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseConnectionManager implements DatabaseConnectionManagerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $connectionsByName;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;

        $this->connectionsByName = array();
    }

    /**
     * {@inheritdoc}
     */
    protected function instanciateDatabaseConnection(
        array $parameters
    ) {
        $driverIdsByDriverName = $this->container->getParameter(
            'dbal.driver_ids_by_driver_name'
        );

        $driverParameter = $parameters['driver'];

        if (!isset($driverIdsByDriverName[$driverParameter])) {
            throw new Exception(
                sprintf(
                    'Found no driver with name "%s".',
                    $driverParameter
                )
            );
        }

        $driverId = $driverIdsByDriverName[$driverParameter];

        $driver = $this->container->get(
            $driverId
        );

        /* @var $driver DriverInterface */

        return $driver->instanciateDatabaseConnection(
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function instanciateNamedDatabaseConnection(
        $name
    ) {
        $configByDatabaseConnectionName = $this->container->getParameter(
            'dbal.parameters_by_database_connection_name'
        );

        if (!isset($configByDatabaseConnectionName[$name])) {
            throw new Exception(
                sprintf(
                    'Found no database connection with name "%s".',
                    $name
                )
            );
        }

        return $this->instanciateDatabaseConnection(
            $configByDatabaseConnectionName[$name]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionWithName(
        $name
    ) {
        if (!isset($this->connectionsByName[$name])) {
            $connection = $this->instanciateNamedDatabaseConnection(
                $name
            );

            $connection->connect();

            $this->connectionsByName[$name] = $connection;
        }

        return $this->connectionsByName[$name];
    }
}
