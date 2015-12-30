<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager;

use Arlekin\Dbal\Migration\SqlBased\Builder\MigrationQueriesBuilderInterface;
use Arlekin\Dbal\SqlBased\Element\Schema;

class DiffManager
{
    /**
     * @var MigrationQueriesBuilderInterface
     */
    protected $migrationQueriesBuilder;

    /**
     * @var callable
     */
    protected $versionGenerator;

    public function __construct(MigrationQueriesBuilderInterface $migrationQueriesBuilder)
    {
        $this->migrationQueriesBuilder = $migrationQueriesBuilder;
    }

    /**
     * Builds the content of a class to migrate
     * from the current database schema to a given destination schema.
     *
     * @param Schema $sourceSchema
     * @param Schema $destinationSchema
     *
     * @return string the content of a migration class
     */
    public function generateDiffFileContent(Schema $sourceSchema, Schema $destinationSchema)
    {
        return $this->doGenerateDiffFileContent(
            $sourceSchema,
            $destinationSchema
        )['content'];
    }

    /**
     * @param Schema $sourceSchema
     * @param Schema $destinationSchema
     * @param string $diffDestinationDirectory
     *
     * @return DiffManager
     */
    public function generateDiffFile(Schema $sourceSchema, Schema $destinationSchema, $diffDestinationDirectory) {
        $result = $this->doGenerateDiffFileContent($sourceSchema, $destinationSchema);

        file_put_contents(
            $this->getFileName(
                $diffDestinationDirectory,
                $result['className']
            ),
            $result['content']
        );

        return $this;
    }

    /**
     * @param Schema $sourceSchema
     * @param Schema $destinationSchema
     *
     * @return array
     */
    protected function doGenerateDiffFileContent(Schema $sourceSchema, Schema $destinationSchema)
    {
        $sqlMigrationsQueries = $this->migrationQueriesBuilder->getMigrationSqlQueries(
            $sourceSchema,
            $destinationSchema
        );

        $upFunctionContent = sprintf('        return [%s', PHP_EOL);

        $upQueries = [];

        foreach($sqlMigrationsQueries as $sqlMigrationsQuery) {
            $upQueries[] = sprintf('            \'%s\',', $sqlMigrationsQuery);
        }

        $upFunctionContent .= implode(PHP_EOL, $upQueries);

        $upFunctionContent .= sprintf('%s        ];', PHP_EOL);

        $versionGenerator = $this->getVersionGenerator();

        $version = $versionGenerator();

        $prefix = 'Version';

        $className = sprintf('%s%s', $prefix, $version);
        
        $outputContent = function () use ($className, $version, $upFunctionContent) {
            ob_start();
            
            include __DIR__.'/../../../../../Migration/Resources/views/migration.php';
            
            $content = ob_get_contents();
            
            ob_end_clean();
            
            return $content;
        };
        
        $content = $outputContent();

        return [
            'className' => $className,
            'content' => $content,
        ];
    }

    /**
     * callable $versionGenerator
     *
     * @return DiffManager
     */
    public function setVersionGenerator(callable $versionGenerator)
    {
        $this->versionGenerator = $versionGenerator;

        return $this;
    }

    /**
     * @param strng $diffDestinationDirectory
     * @param string $className
     *
     * @return string
     */
    protected function getFileName($diffDestinationDirectory, $className)
    {
        return sprintf('%s/%s.php', $diffDestinationDirectory, $className);
    }

    /**
     * @return callable
     */
    protected function getVersionGenerator()
    {
        if (isset($this->versionGenerator)) {
            $val = $this->versionGenerator;
        } else {
            $val = function () {
                $now = new \DateTime();

                return $now->format('YmdHis');
            };
        }

        return $val;
    }
}