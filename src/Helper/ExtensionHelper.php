<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\Helper;

use Arlecchino\DatabaseAbstractionLayer\DriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExtensionHelper
{
    /**
     * @param ContainerInterface $container
     *
     * @return array
     */
    public static function getDriverIdsByDriverNameFromTaggedServiceIds(
        ContainerInterface $container
    ) {
        $dbalDriversTaggedServiceIds = $container->findTaggedServiceIds(
            'dbal.driver'
        );

        $driverIdsByDriverName = array();

        foreach (array_keys($dbalDriversTaggedServiceIds) as $serviceId) {
            $driver = $container->get($serviceId);
            /* @var $driver DriverInterface */
            $driverIdsByDriverName[$driver->getName()] = $serviceId;
        }

        return $driverIdsByDriverName;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public static function getParametersByDatabaseConnectionName(
        array $config
    ) {
        $parametersByDatabaseConnectionName = array();

        if (isset($config['connections'])) {
            foreach ($config['connections'] as $connectionConfig) {
                $parametersByDatabaseConnectionName[$connectionConfig['name']] = $connectionConfig;
            }
        }

        return $parametersByDatabaseConnectionName;
    }
}
