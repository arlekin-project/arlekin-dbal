<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Migration\Builder;

use Calam\Dbal\Driver\Pdo\MySql\DatabaseConnection;
use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnDataTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexTypes;
use Calam\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Schema;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Element\View;

/**
 * Builds a MySql\Element\Schema from a MySQL database.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class SchemaBuilder
{
    /**
     * Builds and gets a Schema from the SQL-based database
     * that the provided $connection is configured to use.
     *
     * @param DatabaseConnection $connection
     *
     * @return Schema
     */
    public function getFromDatabase(DatabaseConnection $connection)
    {
        $schema = new Schema();

        $result = $connection->executeMultipleQueries(
            [
                "
                SELECT
                    table_name AS viewName,
                    view_definition AS viewDefinition
                FROM `information_schema`.`views`
                ",
                "
                (SELECT
                    Tables.TABLE_NAME AS tableName,
                    Tables.TABLE_TYPE AS tableType,
                    Columns.COLUMN_NAME AS columnName,
                    Columns.EXTRA AS columnExtra,
                    UPPER(Columns.DATA_TYPE) AS columnDataType,
                    Columns.COLUMN_TYPE AS columnType,
                    Columns.CHARACTER_MAXIMUM_LENGTH AS columnCharacterMaximumLength,
                    Columns.NUMERIC_PRECISION AS columnNumericPrecision,
                    Columns.CHARACTER_MAXIMUM_LENGTH AS columnLength,
                    Columns.IS_NULLABLE AS columnNullable
                FROM `information_schema`.`tables` AS Tables
                INNER JOIN `information_schema`.`columns` AS Columns
                    ON Columns.TABLE_NAME = Tables.TABLE_NAME
                WHERE Tables.table_schema = DATABASE()
                ORDER BY tableName ASC, Columns.ORDINAL_POSITION ASC)
                ",
                "
                (SELECT
                    Tables.TABLE_NAME AS tableName,
                    Tables.TABLE_TYPE AS tableType,
                    Statistics.NON_UNIQUE AS statNonUnique,
                    Statistics.INDEX_NAME AS statIndexName,
                    Statistics.COLUMN_NAME AS statColumnName,
                    Statistics.INDEX_TYPE AS statIndexType
                FROM `information_schema`.`tables` AS Tables
                INNER JOIN `information_schema`.`statistics` AS Statistics
                    ON Statistics.TABLE_NAME = Tables.TABLE_NAME
                WHERE Tables.table_schema = DATABASE()
                ORDER BY tableName ASC, Statistics.SEQ_IN_INDEX ASC)
                ",
                "
                (SELECT
                    kcu.CONSTRAINT_NAME AS constraintName,
                    kcu.TABLE_NAME AS tableName,
                    kcu.COLUMN_NAME AS columnName,
                    kcu.REFERENCED_TABLE_NAME AS referencedTableName,
                    kcu.REFERENCED_COLUMN_NAME AS referencedColumnName,
                    rc.UPDATE_RULE AS updateRule,
                    rc.DELETE_RULE AS deleteRule
                    FROM information_schema.KEY_COLUMN_USAGE AS kcu
                    INNER JOIN `information_schema`.`REFERENTIAL_CONSTRAINTS` AS rc
                        ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                            AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                    WHERE kcu.CONSTRAINT_SCHEMA LIKE DATABASE()
                        AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
                    ORDER BY
                        kcu.TABLE_NAME,
                        kcu.CONSTRAINT_NAME,
                        kcu.REFERENCED_TABLE_NAME)
                ",
            ]
        );

        $tablesByName = [];
        $viewsByName = [];
        $tableRowsByTableName = [];

        foreach ($result[0] as $row) {
            $viewName = $row['viewName'];
            $viewDefinition = $row['viewDefinition'];

            $view = new View();

            $view->setName(
                $viewName
            )->setDefinition(
                $viewDefinition
            );

            $viewsByName[$viewName] = $view;
        }

        foreach ($result[1] as $row) {
            $tableName = $row['tableName'];
            $tableType = $row['tableType'];

            if ($tableType === 'BASE TABLE') {
                if (!isset($tablesByName[$tableName])) {
                    $table = new Table();

                    $table->setName($tableName);

                    $tablesByName[$tableName] = $table;
                    $tableRowsByTableName[$tableName] = [];
                }

                $columnName = $row['columnName'];
                $columnExtra = $row['columnExtra'];
                $columnType = $row['columnType'];
                $columnDataType = $row['columnDataType'];
                $columnCharacterMaximumLength = $row['columnCharacterMaximumLength'];
                $columnNumericPrecision = $row['columnNumericPrecision'];

                $upperDataType = strtoupper($columnDataType);

                if ($upperDataType === ColumnDataTypes::TYPE_ENUM) {
                    $columnLength = null;
                } elseif ($upperDataType === ColumnDataTypes::TYPE_INT) {
                    $columnLength = $columnNumericPrecision + 1;
                } else {
                    $columnLength = $columnCharacterMaximumLength;
                }

                $rawColumnNullable = $row['columnNullable'];

                $columnNullable = $rawColumnNullable === 'YES' ? true : false;

                $column = new Column();

                $column->setName(
                    $columnName
                )->setDataType(
                    $upperDataType
                );

                if (in_array($upperDataType, ColumnDataTypes::$WITH_LENGTH_TYPES)) {
                    $column->setParameter('length', $columnLength);
                }

                if ($columnExtra === 'auto_increment') {
                    $column->setAutoIncrementable(true);
                }

                if (strtoupper($columnDataType) === ColumnDataTypes::TYPE_ENUM) {
                    $commaSeparatedCollection = substr(
                        $columnType,
                        5,
                        strlen($columnType) - 6
                    );

                    $splitCollection = preg_split(
                        "/(?<='),(?=')/",
                        $commaSeparatedCollection
                    );

                    for ($i = 0; $i < count($splitCollection); $i++) {
                        $splitCollection[$i] = trim(
                            $splitCollection[$i],
                            '\''
                        );
                    }

                    $column->setParameter('allowedValues', $splitCollection);
                }
                $column->setNullable($columnNullable);

                $tableRowsByTableName[$tableName][$columnName] = $column;
            }
        }

        $tablePrimaryKeyByTableName = [];
        $tableIndexesByTableName = [];

        foreach ($result[2] as $row) {
            $tableType = $row['tableType'];

            if ($tableType === 'BASE TABLE') {
                $tableName = $row['tableName'];
                $statNonUnique = $row['statNonUnique'];
                $statIndexName = $row['statIndexName'];
                $statColumnName = $row['statColumnName'];
                $statIndexType = $row['statIndexType'];

                if ($statIndexName === 'PRIMARY') {
                    $tablePrimaryKeyByTableName[$tableName]['columns'][$statColumnName] = $statColumnName;
                } else {
                    $tableIndexesByTableName[$tableName][$statIndexName]['name'] = $statIndexName;
                    $tableIndexesByTableName[$tableName][$statIndexName]['columns'][$statColumnName] = $statColumnName;
                    $tableIndexesByTableName[$tableName][$statIndexName]['unique'] = !$statNonUnique;
                    $tableIndexesByTableName[$tableName][$statIndexName]['kind'] = $statIndexType;
                }
            }
        }

        $foreignKeysByTableName = [];

        foreach ($result[3] as $row) {
            $constraintName = $row['constraintName'];
            $tableName = $row['tableName'];
            $columnName = $row['columnName'];
            $referencedTableName = $row['referencedTableName'];
            $referencedColumnName = $row['referencedColumnName'];
            $deleteRule = $row['deleteRule'];
            $updateRule = $row['updateRule'];

            $foreignKeysByTableName[$tableName][$constraintName]['referencedTableName'] = $referencedTableName;
            $foreignKeysByTableName[$tableName][$constraintName]['columnNames'][] = $columnName;
            $foreignKeysByTableName[$tableName][$constraintName]['referencedColumnNames'][] = $referencedColumnName;
            $foreignKeysByTableName[$tableName][$constraintName]['onDelete'] = $deleteRule;
            $foreignKeysByTableName[$tableName][$constraintName]['onUpdate'] = $updateRule;
        }

        foreach ($tablesByName as $tableName => $table) {
            /* @var $table Table */

            $table->setColumns(
                array_values(
                    $tableRowsByTableName[$tableName]
                )
            );

            if (isset($tablePrimaryKeyByTableName[$tableName])) {
                $primaryKey = new PrimaryKey();

                $columnsNames = $tablePrimaryKeyByTableName[$tableName]['columns'];

                foreach ($columnsNames as $columnName) {
                    $primaryKey->addColumn($tableRowsByTableName[$tableName][$columnName]);
                }

                $table->setPrimaryKey($primaryKey);
            }

            if (isset($tableIndexesByTableName[$tableName])) {
                foreach ($tableIndexesByTableName[$tableName] as $arrIndex) {
                    $index = new Index();

                    $index->setName($arrIndex['name']);

                    $columnsNames = $arrIndex['columns'];

                    foreach ($columnsNames as $columnName) {
                        $index->addColumn($tableRowsByTableName[$tableName][$columnName]);
                    }

                    if ($arrIndex['unique']) {
                        $index->setType(IndexTypes::KIND_UNIQUE);
                    } else {
                        $index->setType($arrIndex['kind']);
                    }

                    $table->addIndex($index);
                }
            }
        }

        foreach ($tablesByName as $tableName => $table) {
            /* @var $table Table */

            if (isset($foreignKeysByTableName[$tableName])) {
                foreach ($foreignKeysByTableName[$tableName] as $arrForeignKey) {
                    $referencedTableName = $arrForeignKey['referencedTableName'];
                    $columnNames = $arrForeignKey['columnNames'];
                    $referencedColumnNames = $arrForeignKey['referencedColumnNames'];

                    $foreignKey = new ForeignKey();

                    $foreignKey->setReferencedTable($tablesByName[$referencedTableName]);

                    foreach ($columnNames as $columnName) {
                        $foreignKey->addColumn($tableRowsByTableName[$tableName][$columnName]);
                    }

                    foreach ($referencedColumnNames as $referencedColumnName) {
                        $foreignKey->addReferencedColumn($tableRowsByTableName[$referencedTableName][$referencedColumnName]);
                    }

                    $foreignKey->setOnDelete(
                        $arrForeignKey['onDelete']
                    )->setOnUpdate(
                        $arrForeignKey['onUpdate']
                    );

                    $table->addForeignKey($foreignKey);
                }
            }
        }

        $schema->setTables(
            array_values(
                $tablesByName
            )
        )->setViews(
            array_values(
                $viewsByName
            )
        );

        return $schema;
    }
}
