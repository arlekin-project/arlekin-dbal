<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Migration\Builder;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\View;
use Arlekin\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper;
use Arlekin\Dbal\Helper\ArrayHelper;

/**
 * Builds MySQL migration queries.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class MigrationQueriesBuilder
{
    /**
     * Builds and gets the MySQL queries to migrate
     * from the current database schema to a given destination schema.
     *
     * @param Schema $sourceSchema
     * @param Schema $destinationSchema
     *
     * @return array a collection of queries
     */
    public function getMigrationSqlQueries(Schema $sourceSchema, Schema $destinationSchema)
    {
        $dropViewsQueries = [];
        $dropTablesQueries = [];
        $dropColumnsQueries = [];
        $dropIndexesQueries = [];
        $dropForeignKeysQueries = [];
        $alterTableUnsetAutoIncrementQueries = [];
        $dropPrimaryKeysQueries = [];
        $tablesBasesSqlQueries = [];
        $createColumnsQueries = [];
        $createPrimaryKeysSqlQueries = [];
        $alterTableSetAutoIncrementQueries = [];
        $createIndexesSqlQueries = [];
        $foreignKeysSqlQueries = [];
        $createViewsQueries = [];
        $alterViewsQueries = [];

        $this->makeAlterTableUnsetAutoIncrementQueries(
            $alterTableUnsetAutoIncrementQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropPrimaryKeyQueries(
            $dropPrimaryKeysQueries,
            $createPrimaryKeysSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropForeignKeysQueries(
            $dropForeignKeysQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropIndexesQueries(
            $dropIndexesQueries,
            $createIndexesSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropColumnsQueries(
            $dropColumnsQueries,
            $createColumnsQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropViewsQueries(
            $dropViewsQueries,
            $sourceSchema,
            $destinationSchema
        )->makeDropTableQueries(
            $dropTablesQueries,
            $sourceSchema,
            $destinationSchema
        )->makeCreateTableBaseQueries(
            $tablesBasesSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeCreatePrimaryKeysSqlQueries(
            $createPrimaryKeysSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeAlterTableSetAutoIncrementQueries(
            $alterTableSetAutoIncrementQueries,
            $sourceSchema,
            $destinationSchema
        )->makeCreateIndexesSqlQueries(
            $createIndexesSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeAlterTableCreateForeignKeysQueries(
            $foreignKeysSqlQueries,
            $sourceSchema,
            $destinationSchema
        )->makeAlterTableCreateColumnsQueries(
            $createColumnsQueries,
            $sourceSchema,
            $destinationSchema
        )->makeCreateViewsQueries(
            $createViewsQueries,
            $sourceSchema,
            $destinationSchema
        )->makeAlterViewsQueries(
            $alterViewsQueries,
            $sourceSchema,
            $destinationSchema
        );

        return array_merge(
            $alterTableUnsetAutoIncrementQueries,
            $dropPrimaryKeysQueries,
            $dropForeignKeysQueries,
            $dropIndexesQueries,
            $dropColumnsQueries,
            $dropViewsQueries,
            $dropTablesQueries,
            $tablesBasesSqlQueries,
            $createColumnsQueries,
            $createPrimaryKeysSqlQueries,
            $alterTableSetAutoIncrementQueries,
            $createIndexesSqlQueries,
            $foreignKeysSqlQueries,
            $createViewsQueries,
            $alterViewsQueries
        );
    }

    /**
     * Make the queries to drop the tables
     * existing in the original schema
     * but not in the destination schema.
     *
     * @param array &$dropTablesQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeDropTableQueries(array &$dropTablesQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $originalTables = $originalSchema->getTables();

        //Remove table existing in DB and not in schema
        foreach ($originalTables as $originalTable) {
            $originTableName = $originalTable->getName();

            if (!$destinationSchema->hasTableWithName($originTableName)) {
                $dropTableQuery = 'DROP TABLE '
                    .MySqlHelper::backquoteTableOrColumnName($originTableName);

                $dropTablesQueries[] = $dropTableQuery;
            }
        }

        return $this;
    }

    /**
     * Make the queries to drop columns
     * if they do exist in the original schema
     * but not in the destination schema, that is :
     * - if the destination schema table exists but the column does not
     * - if the destination schema table exists and the column definition
     * in both the original schema and the destination schema are not the same.
     * It also handle the re-creation of the column if its definition changes.
     *
     * @param array &$dropColumnsQueries
     * @param array &$createColumnsQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeDropColumnsQueries(
        array &$dropColumnsQueries,
        array &$createColumnsQueries,
        Schema $originalSchema,
        Schema $destinationSchema
    ) {
        $originalTables = $originalSchema->getTables();

        foreach ($originalTables as $originalTable) {
            $originalTableName = $originalTable->getName();

            //Remove the column
            //if the table exists in the destination schema
            if ($destinationSchema->hasTableWithName($originalTableName)) {
                $destinationTable = $destinationSchema->getTableWithName($originalTableName);
                $originalColumns = $originalTable->getColumns();

                foreach ($originalColumns as $originalColumn) {
                    $columnName = $originalColumn->getName();
                        //but the the column does not
                        if (!$destinationTable->hasColumnWithName($columnName)) {
                            $doDropColumn = true;
                        } else {
                            //or the column does not
                            //have the same definition
                            $destinationColumn = $destinationTable->getColumnWithName($columnName);

                            $areSameIgnoreAutoIncrement = MySqlHelper::columnsAreSameIgnoreAutoIncrement(
                                $destinationColumn,
                                $originalColumn
                            );

                            if (!$areSameIgnoreAutoIncrement) {
                                $doDropColumn = true;

                                $createColumnsQueries[] = MySqlHelper::generateCreateAlterTableCreateColumnSql($destinationColumn);
                            } else {
                                $doDropColumn = false;
                            }
                        }

                        if ($doDropColumn) {
                            $dropColumnQuery = 'ALTER TABLE '
                                .MySqlHelper::backquoteTableOrColumnName(
                                    $originalTableName
                                )
                                .' DROP COLUMN '
                                .MySqlHelper::backquoteTableOrColumnName(
                                    $columnName
                                );

                            $dropColumnsQueries[] = $dropColumnQuery;
                        }
                }
            }
        }

        return $this;
    }

    /**
     * Make the queries to drop indexes
     * if they do exist in the original schema
     * but not in the destination schema, that is :
     * - if the destination schema table exists but the index does not
     * - if the destination schema table exists and the index definition
     * in both the original schema and the destination schema are not the same.
     * It also handle the re-creation of the index if its definition changes.
     *
     * @param array &$dropIndexesQueries
     * @param array &$createIndexesSqlQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeDropIndexesQueries(
        array &$dropIndexesQueries,
        array &$createIndexesSqlQueries,
        Schema $originalSchema,
        Schema $destinationSchema
    ) {
        $originalTables = $originalSchema->getTables();

        //Remove indexes existing in DB and not in schema
        foreach ($originalTables as $originalTable) {
            $originalTableName = $originalTable->getName();

            //Remove the index
            //if the table exists
            if ($destinationSchema->hasTableWithName($originalTableName)) {
                $destinationTable = $destinationSchema->getTableWithName($originalTableName);
                $originalIndexes = $originalTable->getIndexes();

                foreach ($originalIndexes as $originalIndex) {
                    $originalIndexName = $originalIndex->getName();

                    //but the
                    //the index does not
                    if (!$destinationTable->hasIndexWithName($originalIndexName)) {
                        $doDropIndex = true;
                    } else {
                        //or the index does not
                        //have the same definition

                        $originalIndexAsArray = $originalIndex->toArray();
                        $schemaIndex = $destinationTable->getIndexWithName($originalIndexName);
                        $schemaIndexAsArray = $schemaIndex->toArray();

                        $areSame = ArrayHelper::arraysAreSameRecursive($originalIndexAsArray, $schemaIndexAsArray);

                        if (!$areSame) {
                            $doDropIndex = true;

                            $createIndexesSqlQueries[] = MySqlHelper::generateCreateAlterTableCreateIndexSql($schemaIndex);
                        } else {
                            $doDropIndex = false;
                        }
                    }

                    if ($doDropIndex) {
                        $dropIndex = 'DROP INDEX '
                            .$originalIndexName
                            .' ON '
                            .MySqlHelper::backquoteTableOrColumnName(
                                $originalTableName
                            );

                        $dropIndexesQueries[] = $dropIndex;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Make the queries to drop the foreign keys
     * if they do no exist in the original schema
     * but not in the destination schema.
     *
     * @param array &$dropForeignKeysQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeDropForeignKeysQueries(array &$dropForeignKeysQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $originalTables = $originalSchema->getTables();

        foreach ($originalTables as $originalTable) {
            $originalTableName = $originalTable->getName();

            //Remove the foreign key
            //if the table exists
            if ($destinationSchema->hasTableWithName($originalTableName)) {
                $destinationTable = $destinationSchema->getTableWithName($originalTableName);
                $originalForeignKeys = $originalTable->getForeignKeys();

                foreach ($originalForeignKeys as $originalForeignKey) {
                    $originalForeignKeyAsArray = $originalForeignKey->toArray();
                    $originalColumnsNames = $originalForeignKeyAsArray['columns'];
                    $originalReferencedTableName = $originalForeignKeyAsArray['referencedTable'];
                    $originalReferencedColumnsNames = $originalForeignKeyAsArray['referencedColumns'];

                    //but the
                    //the foreign key does not
                    if (!$destinationTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed($originalColumnsNames, $originalReferencedTableName, $originalReferencedColumnsNames)) {
                        $stringId = MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray($originalForeignKeyAsArray);

                        $dropIndex = 'ALTER TABLE '
                            .MySqlHelper::backquoteTableOrColumnName(
                                $originalTableName
                            )
                            .' DROP FOREIGN KEY '
                            .MySqlHelper::backquoteTableOrColumnName(
                                $stringId
                            );

                        $dropForeignKeysQueries[] = $dropIndex;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Make the query to alter a table to create its primary key.
     *
     * @param array &$createPrimaryKeysSqlQueries
     * @param string $tableName
     * @param array $columnsNames
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeAlterTableAddPrimaryKeyQuery(array &$createPrimaryKeysSqlQueries, $tableName, array $columnsNames)
    {
        $sqlPrimaryKey = 'ALTER TABLE '
            .MySqlHelper::backquoteTableOrColumnName(
                $tableName
            )
            .' ADD PRIMARY KEY '
            .MySqlHelper::generateSqlCollectionBetweenParentheses(
                MySqlHelper::backquoteArrayOfTableOrColumnNames(
                    $columnsNames
                )
            );

        $createPrimaryKeysSqlQueries[] = $sqlPrimaryKey;

        return $this;
    }

    /**
     * Make the queries to drop the primary key of a table
     * if it does exist in the original schema
     * but not in the destination schema, that is :
     * - if the destination schema table exists but the primary does not
     * - if the destination schema table exists and the primary key definition
     * in both the original schema and the destination schema are not the same.
     * It also handle the re-creation of the primary key if its definition changes.
     *
     * @param array $dropPrimaryKeysQueries
     * @param array $createPrimaryKeysSqlQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeDropPrimaryKeyQueries(
        array &$dropPrimaryKeysQueries,
        array &$createPrimaryKeysSqlQueries,
        Schema $originalSchema,
        Schema $destinationSchema
    ) {
        $originalTables = $originalSchema->getTables();

        //Remove primary keys existing in DB and not in schema
        foreach ($originalTables as $originalTable) {
            $originalTableName = $originalTable->getName();

            //Remove the primary
            //if the table exists but the
            //the primary does not.
            if ($destinationSchema->hasTableWithName($originalTableName) && $originalTable->getPrimaryKey() !== null) {
                $destinationTable = $destinationSchema->getTableWithName($originalTableName);

                $originalPrimaryKeyAsArray = $originalTable->getPrimaryKey()->toArray();

                if (!$destinationTable->hasPrimaryKeyWithColumnsNamed($originalPrimaryKeyAsArray['columns'])) {
                    $dropPrimaryKeyQuery = 'ALTER TABLE '
                        .MySqlHelper::backquoteTableOrColumnName(
                            $originalTableName
                        )
                        .' DROP PRIMARY KEY';

                    $dropPrimaryKeysQueries[] = $dropPrimaryKeyQuery;

                    $primaryKey = $destinationTable->getPrimaryKey();

                    if ($primaryKey !== null) {
                        $tablePrimaryKeyColumnNames = [];

                        foreach ($destinationTable->getPrimaryKey()->getColumns() as $column) {
                            $tablePrimaryKeyColumnNames[] = $column->getName();
                        }

                        $this->makeAlterTableAddPrimaryKeyQuery(
                            $createPrimaryKeysSqlQueries,
                            $originalTableName,
                            $tablePrimaryKeyColumnNames
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Make the queries to create the base of the tables
     * if they do exist in the destination schema
     * but not in the original schema.
     *
     * @param array &$tablesBasesSqlQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeCreateTableBaseQueries(array &$tablesBasesSqlQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationTableName = $destinationTable->getName();

            //Create table if not exists
            if (!$originalSchema->hasTableWithName($destinationTableName)) {
                $destinationColumns = $destinationTable->getColumns();

                $sqlTableBases = 'CREATE TABLE ';

                $sqlTableBases .= MySqlHelper::backquoteTableOrColumnName($destinationTableName);

                $sqlTableBases .= ' '.MySqlHelper::generateCreateTableColumnsSql($destinationColumns);

                $sqlTableBases .= ' DEFAULT CHARACTER SET utf8 COLLATE utf8_bin';

                $tablesBasesSqlQueries[] = $sqlTableBases;
            }
        }

        return $this;
    }

    /**
     * Make the queries to create the primary keys of the tables
     * if they exist in the destination schema
     * but not in the original schema, that is :
     * - if the primary key's table does not exist in the original schema
     * - if the primary key's table does exist in the original schema
     * but the key itself does not
     *
     * @param array &$createPrimaryKeysSqlQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeCreatePrimaryKeysSqlQueries(array &$createPrimaryKeysSqlQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationTableName = $destinationTable->getName();
            //Create primary if not exists,
            $destinationPrimaryKey = $destinationTable->getPrimaryKey();

            //that is either if table does not exist in the original schema
            if (!$originalSchema->hasTableWithName($destinationTableName)) {
                $doAddPrimaryKey = $destinationPrimaryKey !== null;
            //or it does but the key itself does not exist
            } else {
                $originalTable = $originalSchema->getTableWithName($destinationTableName);

                $doAddPrimaryKey = $originalTable->getPrimaryKey() === null && $destinationTable->getPrimaryKey() !== null;
            }

            if ($doAddPrimaryKey) {
                $destinationColumns = $destinationPrimaryKey->getColumns();
                $columnsNames = [];

                foreach ($destinationColumns as $column) {
                    $columnName = $column->getName();
                    $columnsNames[] = $columnName;
                }

                $this->makeAlterTableAddPrimaryKeyQuery(
                    $createPrimaryKeysSqlQueries,
                    $destinationTableName,
                    $columnsNames
                );
            }
        }

        return $this;
    }

    /**
     * Make the queries to create the indexes of the tables
     * if they exist in the destination schema
     * but not in the original schema, that is :
     * - if the index' table does not exist in the original schema
     * - if the index' table does exist in the original schema
     * but the index itself does not
     *
     * @param array &$createIndexesSqlQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeCreateIndexesSqlQueries(array &$createIndexesSqlQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            //Create index if not exists,
            $destinationTableName = $destinationTable->getName();

            //that is either if table does not exists
            if (!$originalSchema->hasTableWithName($destinationTableName)) {
                $indexes = $destinationTable->getIndexes();

                foreach ($indexes as $index) {
                    $query = MySqlHelper::generateCreateAlterTableCreateIndexSql($index);

                    $createIndexesSqlQueries[] = $query;
                }
            //or if table exists in schema but the index does not
            } else {
                $originalTable = $originalSchema->getTableWithName($destinationTableName);

                $indexes = $destinationTable->getIndexes();

                foreach ($indexes as $index) {
                    $indexName = $index->getName();

                    if (!$originalTable->hasIndexWithName($indexName)) {
                        $query = MySqlHelper::generateCreateAlterTableCreateIndexSql($index);

                        $createIndexesSqlQueries[] = $query;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Make the queries to alter the tables to create the columns.
     *
     * @param array &$createColumnsQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeAlterTableCreateColumnsQueries(array &$createColumnsQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationTableName= $destinationTable->getName();

            if ($originalSchema->hasTableWithName($destinationTableName)) {
                $originalTable = $originalSchema->getTableWithName($destinationTableName);
                $destinationColumns = $destinationTable->getColumns();

                foreach ($destinationColumns as $destinationColumn) {
                    $destinationColumnName = $destinationColumn->getName();

                    if (!$originalTable->hasColumnWithName($destinationColumnName)) {
                        $createColumnQuery = MySqlHelper::generateCreateAlterTableCreateColumnSql(
                            $destinationColumn
                        );

                        $createColumnsQueries[] = $createColumnQuery;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * ATM, to be used in makeAlterTableCreateForeignKeysQueries only.
     *
     * @param callable $what
     * @param Table $table
     * @param Schema $originalSchema
     * @param array &$foreignKeysSqlQueries
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeAlterTableCreateForeignKeysQueriesMakeDoForEachForeignKeys(
        callable $what,
        Table $table,
        Schema $originalSchema,
        array &$foreignKeysSqlQueries
    ) {
        $foreignKeys = $table->getForeignKeys();
        $tableName = $table->getName();

        foreach ($foreignKeys as $foreignKey) {
            $referencedTable = $foreignKey->getReferencedTable();
            $referencedTableName = $referencedTable->getName();
            $columnsNames = [];
            $referencedColumnsNames = [];
            $columns = $foreignKey->getColumns();
            $referencedColumns = $foreignKey->getReferencedColumns();

            foreach ($columns as $column) {
                $name = $column->getName();
                $columnsNames[] = $name;
            }

            foreach ($referencedColumns as $referencedColumn) {
                $name = $referencedColumn->getName();
                $referencedColumnsNames[] = $name;
            }

            $what(
                $table,
                $originalSchema,
                $foreignKeysSqlQueries,
                $foreignKey,
                $tableName,
                $columnsNames,
                $referencedTableName,
                $referencedColumnsNames
            );
        }

        return $this;
    }

    /**
     * Make the SQL query to alter a table to create a foreign key.
     *
     * @param array &$foreignKeysSqlQueries
     * @param ForeignKey $foreignKey
     * @param string $tableName
     * @param array $columnsNames
     * @param string $referencedTableName
     * @param array $referencedColumnsNames
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeAlterTableCreateForeignKeysQueriesMakeCreateForeignKeySql(
        array &$foreignKeysSqlQueries,
        ForeignKey $foreignKey,
        $tableName,
        array $columnsNames,
        $referencedTableName,
        array $referencedColumnsNames
    ) {
        $onDelete = $foreignKey->getOnDelete();
        $onUpdate = $foreignKey->getOnUpdate();

        $foreignKeyHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKey($foreignKey);

        $query = 'ALTER TABLE ';

        $query .= MySqlHelper::backquoteTableOrColumnName(
            $tableName
        );

        $query .= ' ADD CONSTRAINT ';

        $query .= MySqlHelper::backquoteTableOrColumnName(
            $foreignKeyHash
        );

        $query .= ' FOREIGN KEY ';

        $query .= MySqlHelper::generateSqlCollectionBetweenParentheses(
            MySqlHelper::backquoteArrayOfTableOrColumnNames(
                $columnsNames
            )
        );

        $query.= ' REFERENCES '
            .MySqlHelper::backquoteTableOrColumnName(
                $referencedTableName
            )
            .' ';

        $query .= MySqlHelper::generateSqlCollectionBetweenParentheses(
            MySqlHelper::backquoteArrayOfTableOrColumnNames(
                $referencedColumnsNames
            )
        );
        $query .= ' ON DELETE '.$onDelete;
        $query .= ' ON UPDATE '.$onUpdate;

        $foreignKeysSqlQueries[] = $query;

        return $this;
    }

    /**
     * Make the queries to alter the tables to create the foreign keys
     * if they do exist in the destination schema
     * but not in the original schema, that is :
     * - if the table containing the foreign key does not exist
     * in the original schema
     * - if the table containing the foreign does exist in the original schema
     * but foreign key does not
     *
     * @param array &$foreignKeysSqlQueries,
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder the current instance
     */
    private function makeAlterTableCreateForeignKeysQueries(array &$foreignKeysSqlQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationTableName = $destinationTable->getName();

            //Create foreign if not exists,
            //that is if table exists and foreign key does not exists
            if ($originalSchema->hasTableWithName($destinationTableName)) {
                $originalTable = $originalSchema->getTableWithName($destinationTableName);

                $what = function (
                    Table $table,
                    Schema $originalSchema,
                    array& $foreignKeysSqlQueries,
                    $foreignKey,
                    $tableName,
                    $columnsNames,
                    $referencedTableName,
                    $referencedColumnsNames
                ) use (&$foreignKeysSqlQueries, $originalTable) {
                    $doCreateForeignKey = !$originalTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
                        $columnsNames,
                        $referencedTableName,
                        $referencedColumnsNames
                    );

                    if ($doCreateForeignKey) {
                        $this->makeAlterTableCreateForeignKeysQueriesMakeCreateForeignKeySql(
                            $foreignKeysSqlQueries,
                            $foreignKey,
                            $tableName,
                            $columnsNames,
                            $referencedTableName,
                            $referencedColumnsNames
                        );
                    }
                };
            //or if table does not exists
            } else {
                $what = function (
                    Table $table,
                    Schema $originalSchema,
                    array& $foreignKeysSqlQueries,
                    $foreignKey,
                    $tableName,
                    $columnsNames,
                    $referencedTableName,
                    $referencedColumnsNames
                ) use (&$foreignKeysSqlQueries) {
                    $this->makeAlterTableCreateForeignKeysQueriesMakeCreateForeignKeySql(
                        $foreignKeysSqlQueries,
                        $foreignKey,
                        $tableName,
                        $columnsNames,
                        $referencedTableName,
                        $referencedColumnsNames
                    );
                };
            }

            $this->makeAlterTableCreateForeignKeysQueriesMakeDoForEachForeignKeys(
                $what,
                $destinationTable,
                $originalSchema,
                $foreignKeysSqlQueries
            );
        }

        return $this;
    }

    /**
     * Make the queries to alter the tables
     * to make columns auto increment.
     *
     * @param array &$alterTableSetAutoIncrementQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder
     */
    private function makeAlterTableSetAutoIncrementQueries(array &$alterTableSetAutoIncrementQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationColumns = $destinationTable->getColumns();
            $originalTableName = $destinationTable->getName();

            //if original table exists
            if ($originalSchema->hasTableWithName($originalTableName)) {
                $originalTable = $originalSchema->getTableWithName($originalTableName);

                foreach ($destinationColumns as $destinationColumn) {
                    if ($destinationColumn ->isAutoIncrement()) {
                        //and it has the column
                        $originalColumnName = $destinationColumn->getName();

                        if ($originalTable->hasColumnWithName($originalColumnName)) {
                            $originalColumn = $originalTable->getColumnWithName($originalColumnName);

                            //and the autoincrement value is not the same as the one in the destination schema
                            $autoIncrement = $originalColumn->isAutoIncrement() !== $destinationColumn ->isAutoIncrement();

                            if ($autoIncrement) {
                                $alterTableSetAutoIncrementQueries[] = MySqlHelper::generateAlterTableSetAutoIncrementSqlQuery(
                                    $destinationColumn,
                                    $destinationTable
                                );
                            }
                        }
                    }
                }
            } else {
                //if the original table does not exists
                foreach ($destinationColumns as $destinationColumn) {
                    if ($destinationColumn ->isAutoIncrement()) {
                        $alterTableSetAutoIncrementQueries[] = MySqlHelper::generateAlterTableSetAutoIncrementSqlQuery(
                            $destinationColumn,
                            $destinationTable
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Makes the ALTER TABLE queries to change existing columns
     * to make them non-autoincrementable if necessary.
     *
     * @param array $alterTableUnsetAutoIncrementQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder
     */
    private function makeAlterTableUnsetAutoIncrementQueries(array &$alterTableUnsetAutoIncrementQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationTables = $destinationSchema->getTables();

        foreach ($destinationTables as $destinationTable) {
            $destinationColumns = $destinationTable->getColumns();
            $originalTableName = $destinationTable->getName();

            //if original table exists
            if ($originalSchema->hasTableWithName($originalTableName)) {
                $originalTable = $originalSchema->getTableWithName($originalTableName);

                foreach ($destinationColumns as $destinationColumn) {
                    if (!$destinationColumn ->isAutoIncrement()) {
                        //and it has the column

                        $originalColumnName = $destinationColumn->getName();

                        if ($originalTable->hasColumnWithName($originalColumnName)) {
                            $originalColumn= $originalTable->getColumnWithName($originalColumnName);

                            //and the autoincrement value is not the same as the one in the destination schema
                            $autoIncrement = $originalColumn->isAutoIncrement() !== $destinationColumn ->isAutoIncrement();

                            if ($autoIncrement) {
                                $alterTableUnsetAutoIncrementQueries[] = MySqlHelper::generateAlterTableUnsetAutoIncrementSqlQuery(
                                    $destinationColumn,
                                    $destinationTable
                                );
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Makes the DROP VIEW queries to drop views existing in the original schema
     * but not in the destination schema.
     *
     * @param array& $dropViewsQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder
     */
    private function makeDropViewsQueries(array &$dropViewsQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $originalSchemaViews = $originalSchema->getViews();

        foreach ($originalSchemaViews as $originalSchemaView) {
            /* @var $originalSchemaView View */

            $originalSchemaViewName = $originalSchemaView->getName();

            if (!$destinationSchema->hasViewWithName($originalSchemaViewName)) {
                $dropViewsQueries[] = sprintf('DROP VIEW %s', $originalSchemaViewName);
            }
        }

        return $this;
    }

    /**
     * Makes CREATE VIEW queries to create views existing
     * in the destination schema but not in the original schema.
     *
     * @param array& $createViewsQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder
     */
    private function makeCreateViewsQueries(array &$createViewsQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationSchemaViews = $destinationSchema->getViews();

        foreach ($destinationSchemaViews as $destinationSchemaView) {
            /* @var $view View */
            $destinationSchemaViewName = $destinationSchemaView->getName();

            if (!$originalSchema->hasViewWithName($destinationSchemaViewName)) {
                $destinationSchemaViewDefinition = $destinationSchemaView->getDefinition();

                $createViewsQueries[] = sprintf(
                    'CREATE VIEW %s AS %s',
                    $destinationSchemaViewName,
                    $destinationSchemaViewDefinition
                );
            }
        }

        return $this;
    }

    /**
     * Makes ALTER VIEW queries to update views existing
     * in both the original schema and the destination schema
     * when the defitions are not the same.
     *
     * @param array& $alterViewsQueries
     * @param Schema $originalSchema
     * @param Schema $destinationSchema
     *
     * @return MigrationQueriesBuilder
     */
    private function makeAlterViewsQueries(array& $alterViewsQueries, Schema $originalSchema, Schema $destinationSchema)
    {
        $destinationSchemaViews = $destinationSchema->getViews();

        foreach ($destinationSchemaViews as $destinationSchemaView) {
            /* @var $view View */

            $destinationSchemaViewName = $destinationSchemaView->getName();

            if ($originalSchema->hasViewWithName($destinationSchemaViewName)) {
                $originalSchemaView = $originalSchema->getViewWithName($destinationSchemaViewName);
                $destinationSchemaViewDefinition = $destinationSchemaView->getDefinition();
                $originalSchemaViewDefinition = $originalSchemaView->getDefinition();

                if (mb_strtolower($originalSchemaViewDefinition) !== mb_strtolower($destinationSchemaViewDefinition)) {
                    $alterViewsQueries[] = sprintf(
                        'ALTER VIEW %s AS %s',
                        $destinationSchemaViewName,
                        $destinationSchemaViewDefinition
                    );
                }
            }
        }

        return $this;
    }
}
