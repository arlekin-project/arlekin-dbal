<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Migration\Manager;

use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;

class DiffManager
{
    /**
     * @var MigrationQueriesBuilder
     */
    private $migrationQueriesBuilder;

    /**
     * @var callable
     */
    private $versionGenerator;

    /**
     * @param MigrationQueriesBuilder $migrationQueriesBuilder
     */
    public function __construct(MigrationQueriesBuilder $migrationQueriesBuilder)
    {
        $this->migrationQueriesBuilder = $migrationQueriesBuilder;
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
    private function doGenerateDiffFileContent(Schema $sourceSchema, Schema $destinationSchema)
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

        $content = <<<EOT
<?php

namespace Application\Migrations;

use Calam\Dbal\Migration\MigrationInterface;

class $className implements MigrationInterface
{
    public function up()
    {
        $upFunctionContent
    }

    public function getVersion()
    {
        return $version;
    }
}
EOT;

        return [
            'className' => $className,
            'content' => $content,
        ];
    }

    /**
     * @param strng $diffDestinationDirectory
     * @param string $className
     *
     * @return string
     */
    private function getFileName($diffDestinationDirectory, $className)
    {
        return sprintf('%s/%s.php', $diffDestinationDirectory, $className);
    }

    /**
     * @return callable
     */
    private function getVersionGenerator()
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