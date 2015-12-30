<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Arlekin\Dbal\Exception\DbalException;
use Arlekin\Dbal\Migration\MigrationInterface;

class MigrationManager
{
    /**
     * @var DatabaseConnectionInterface
     */
    protected $databaseConnection;

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @param string $version
     *
     * @return boolean true if version has already been applied,
     * false otherwise
     */
    public function versionApplied($version)
    {
        $result = $this->databaseConnection->executeQuery(
            sprintf(
                'SELECT COUNT(`version`) as count FROM `%s` WHERE `version` = :version',
                $this->getMigrationTableName()
            ),
            [
                'version' => $version,
            ]
        );

        return isset($result[0]) && $result[0]->get('count') === '1';
    }

    /**
     * @param string $migrationsFolderFullPath
     *
     * @return array
     */
    public function migrate($migrationsFolderFullPath)
    {
        if (empty($migrationsFolderFullPath)) {
            throw new DbalException(
                '$migrationsFolderFullPath may not be empty.'
            );
        }

        $this->createMigrationTableIfNotExists();

        $executedMigrationsQueriesCount = 0;

        $this->databaseConnection->executeQuery('START TRANSACTION');

        $versionsApplied = [];

        try {
            $fileNames = scandir($migrationsFolderFullPath, SCANDIR_SORT_ASCENDING);

            foreach ($fileNames as $filename) {
                $splFileInfo = new \SplFileInfo($migrationsFolderFullPath.DIRECTORY_SEPARATOR.$filename);
                
                $extension = $splFileInfo->getExtension();
                
                if (!($extension === 'php' && strpos($splFileInfo->getBasename(), 'Version') === 0)) {
                    continue;
                }
                
                /* @var $file SplFileInfo */
                
                require $splFileInfo->getRealPath();

                $splFileInfoFilename = $splFileInfo->getFilename();
                
                $classname = substr(
                    $splFileInfoFilename,
                    0,
                    strrpos(
                        $splFileInfoFilename,
                        $extension
                    ) - 1
                );

                $fullClassname = sprintf('Application\Migrations\%s', $classname);

                $migration = new $fullClassname;
                /* @var $migration MigrationInterface */

                $version = $migration->getVersion();

                if (!$this->versionApplied($version)) {
                    $versionsApplied[] = $version;

                    $this->databaseConnection->executeMultipleQueries(
                        $migration->up()
                    );

                    $executedMigrationsQueriesCount += count(
                        $migration->up()
                    );
                }
            }
        } finally {
            $executedMigrationsCount = count($versionsApplied);

            if ($executedMigrationsCount > 0) {
                $queryString = sprintf(
                    'INSERT INTO `%s` VALUES (:versionsApplied)',
                    $this->getMigrationTableName()
                );

                $this->databaseConnection->executeQuery($queryString, $versionsApplied);
            }

            $this->databaseConnection->executeQuery('COMMIT');
        }

        return [
            'result' => 'success',
            'info' => [
                'executedMigrationsCount' => $executedMigrationsCount,
                'executedMigrationsQueriesCount' => $executedMigrationsQueriesCount,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getMigrationTableName()
    {
        return '_migration';
    }

    /**
     * As the name implies, execute a query to create the migration table
     * if it does not exist.
     */
    protected function createMigrationTableIfNotExists()
    {
        $this->databaseConnection->executeQuery(
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%s` (`version` VARCHAR(255) NOT NULL, PRIMARY KEY (`version`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin',
                $this->getMigrationTableName()
            )
        );
    }
}
