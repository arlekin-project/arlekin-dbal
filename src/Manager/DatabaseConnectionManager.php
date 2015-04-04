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
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function instanciateDatabaseConnection(
        array $parameters
    ) {
        $driverIdsByDriverName = $this->container->getParameter(
            'dbal.driver_ids_by_driver_name'
        );

        $driverParameter = $parameters['driver'];

        if (!array_key_exists($driverParameter, $driverIdsByDriverName)) {
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
    public function instanciateNamedDatabaseConnection(
        $name
    ) {
        $configByDatabaseConnectionName = $this->container->getParameter(
            'dbal.parameters_by_database_connection_name'
        );

        if (!array_key_exists($name, $configByDatabaseConnectionName)) {
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
}
