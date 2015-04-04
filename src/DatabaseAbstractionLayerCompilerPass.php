<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer;

use Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DatabaseAbstractionLayerCompilerPass implements CompilerPassInterface
{
    public function process(
        ContainerBuilder $container
    ) {
        $driverIdsByDriverName = ExtensionHelper::getDriverIdsByDriverNameFromTaggedServiceIds(
            $container
        );

        $container->setParameter(
            'dbal.driver_ids_by_driver_name',
            $driverIdsByDriverName
        );
    }
}
