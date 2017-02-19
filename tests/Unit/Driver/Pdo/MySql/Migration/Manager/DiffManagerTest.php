<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Migration\Manager;

use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Builder\MigrationQueriesBuilder;
use Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager;
use Calam\Dbal\Migration\MigrationInterface;
use Calam\Dbal\Tests\BaseTest;

class DiffManagerTest extends BaseTest
{
    /**
     * @var DiffManager
     */
    protected $diffManager;

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::__construct
     */
    public function testConstruct()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::generateDiffFileContent
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::doGenerateDiffFileContent
     */
    public function testGenerateDiffFileContent()
    {
        $sourceSchema = $this->getBaseSourceSchema();

        $destinationSchema = new Schema();

        $this->doTestGenerateDiffFileContent($sourceSchema, $destinationSchema);
    }

    /**
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::generateDiffFile
     * @covers Calam\Dbal\Driver\Pdo\MySql\Migration\Manager\DiffManager::doGenerateDiffFileContent
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

        $migrationQueriesBuilder = new MigrationQueriesBuilder();

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
