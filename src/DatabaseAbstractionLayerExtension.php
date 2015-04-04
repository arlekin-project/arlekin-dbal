<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\DatabaseAbstractionLayer;

use Arlekin\Core\AbstractBaseArlekinExtension;
use Arlekin\DatabaseAbstractionLayer\DatabaseAbstractionLayerCompilerPass;
use Arlekin\DatabaseAbstractionLayer\Helper\ExtensionHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class DatabaseAbstractionLayerExtension extends AbstractBaseArlekinExtension
{
    /**
     * {@inheritdoc}
     */
    public function getCompilerPasses()
    {
        return array(
            new DatabaseAbstractionLayerCompilerPass()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'dbal';
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        array $configs,
        ContainerBuilder $container
    ) {
        $merged = array();

        foreach ($configs as $config) {
            $merged = array_merge_recursive(
                $merged,
                $config
            );
        }

        $xmlFileLoader = new XmlFileLoader(
            $container,
            new FileLocator(
                __DIR__
            )
        );

        $xmlFileLoader->load(
            __DIR__ . '/../services.xml'
        );

        $parametersByDatabaseConnectionName = ExtensionHelper::getParametersByDatabaseConnectionName(
            $merged
        );

        $container->setParameter(
            'dbal.parameters_by_database_connection_name',
            $parametersByDatabaseConnectionName
        );
    }
}
