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
use Arlekin\Dbal\Migration\Manager\MigrationManagerInterface;
use Arlekin\Dbal\Migration\MigrationInterface;
use Arlekin\Dbal\SqlBased\DatabaseConnectionInterface;
use Arlekin\Dbal\SqlBased\Query;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MigrationManager implements MigrationManagerInterface
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
     * {@inheritdoc}
     */
    public function versionApplied($version)
    {
        $getExistingVersionsQuery = new Query();

        $getExistingVersionsQuery->setSql(
            sprintf(
                'SELECT COUNT(`version`) as count FROM `%s` WHERE `version` = :version',
                $this->getMigrationTableName()
            )
        )->setParameter(
            'version',
            $version
        );

        $result = $this->databaseConnection->executeQuery(
            $getExistingVersionsQuery
        )->getRows();

        return isset($result[0]) && $result[0]->get('count') === '1';
    }

    /**
     * {@inheritdoc}
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
            $finder = new Finder();

            //All files whose name starts with Version
            $finder->files()
                ->in(
                    $migrationsFolderFullPath
                )->filter(
                    function (
                        SplFileInfo $splFileInfo
                    ) {
                        return $splFileInfo->getExtension() === 'php' && strpos(
                            $splFileInfo->getBasename(),
                            'Version'
                        ) === 0;
                    }
                )->sortByName();

            foreach ($finder as $file) {
                /* @var $file SplFileInfo */
                require $file->getRealPath();

                $classname = substr(
                    $file->getFilename(),
                    0,
                    strrpos(
                        $file->getFilename(),
                        $file->getExtension()
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
                    'INSERT INTO `%s` VALUES (%s)',
                    $this->getMigrationTableName(),
                    implode(
                        '), (',
                        array_fill(
                            0,
                            $executedMigrationsCount,
                            '?'
                        )
                    )
                );

                $insertAppliedVersionQuery = new Query();

                $insertAppliedVersionQuery->setSql($queryString);

                foreach ($versionsApplied as $key => $version) {
                    $insertAppliedVersionQuery->setParameter($key, $version);
                }

                $this->databaseConnection->executeQuery($insertAppliedVersionQuery);
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
