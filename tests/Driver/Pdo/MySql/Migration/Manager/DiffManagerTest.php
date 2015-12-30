<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Tests\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\SchemaManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Manager\TableManager;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager;
use Arlekin\Dbal\Migration\MigrationInterface;
use Arlekin\Dbal\Tests\Driver\Pdo\MySql\AbstractBasePdoMySqlTest;

class DiffManagerTest extends AbstractBasePdoMySqlTest
{
    /**
     * @var DiffManager
     */
    protected $diffManager;

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::__construct
     */
    public function testConstruct()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::generateDiffFileContent
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::doGenerateDiffFileContent
     */
    public function testGenerateDiffFileContent()
    {
        $sourceSchema = $this->getBaseSourceSchema();

        $destinationSchema = new Schema();

        $this->doTestGenerateDiffFileContent($sourceSchema, $destinationSchema);
    }

    /**
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::generateDiffFile
     * @covers Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::doGenerateDiffFileContent
     */
    public function testGenerateDiffFile()
    {
        $sourceSchema = $this->getBaseSourceSchema();

        $destinationSchema = new Schema();

        $this->diffManager->generateDiffFile(
            $sourceSchema,
            $destinationSchema,
            sys_get_temp_dir()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $tableManager = new TableManager();
        $schemaManager = new SchemaManager();

        $migrationQueriesBuilder = new MigrationQueriesBuilder($tableManager, $schemaManager);

        $this->diffManager = new DiffManager($migrationQueriesBuilder);
    }

    protected function assertClassInFileIsSubclassOfMigrationInterface($filename)
    {
        $declaredBefore = get_declared_classes();

        require $filename;

        $declaredAfter = get_declared_classes();

        $diff = array_values(
            array_diff(
                $declaredAfter,
                $declaredBefore
            )
        );

        $reflectionClass = new \ReflectionClass($diff[0]);

        $this->assertTrue(
            $reflectionClass->isSubclassOf(
                MigrationInterface::class
            )
        );
    }

    protected function doTestGenerateDiffFileContent(Schema $sourceSchema, Schema $destinationSchema)
    {
        $content = $this->diffManager->generateDiffFileContent($sourceSchema, $destinationSchema);
        
        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];

        file_put_contents($tmpFilename, $content);

        $this->assertClassInFileIsSubclassOfMigrationInterface($tmpFilename);

        fclose($tmpHandle);
    }

    /**
     * @return Schema
     */
    protected function getBaseSourceSchema()
    {
        $sourceSchema = new Schema();

        $table = new Table();
        $table1 = new Table();

        $sourceSchema->addTable(
            $table
        )->addTable(
            $table1
        );

        return $sourceSchema;
    }
}
