<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql\Helper;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ColumnType;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\IndexType;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Calam\Dbal\Helper\ArrayHelper;

/**
 * To help dealing with MySQL database related operations.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class MySqlHelper
{
    /**
     * Returns the given string wrapped in parentheses.
     *
     * @param string $sql
     *
     * @return string
     */
    public static function betweenParentheses($sql)
    {
        return "($sql)";
    }

    /**
     * Generates an SQL collection, that is a comma separated
     * list of the elements of the given collection.
     *
     * @param array $collection
     *
     * @return string
     */
    public static function generateSqlCollection(array $collection)
    {
        return implode(', ', $collection);
    }

    /**
     * Generates an SQL collection, that is a comma separated
     * list of the elements of the given collection.
     * The generated SQL collection is, what's more, wrapped in parentheses.
     *
     * @param array $collection
     *
     * @return string
     */
    public static function generateSqlCollectionBetweenParentheses(array $collection)
    {
        return self::betweenParentheses(
            self::generateSqlCollection($collection)
        );
    }

    /**
     * Returns the given string wrapped in single quotes.
     * MAKE SURE though, as the given string isn't escaped,
     * not to use this function to wrap non-escaped user input
     *
     * @param string $string
     *
     * @return string
     */
    public static function wrapString($string)
    {
        return self::doWrap($string, '\'');
    }

    /**
     * Returns a copy of the collection given as parameter,
     * with each elements wrapped in single quotes.
     * MAKE SURE though, as the given string isn't escaped,
     * not to use this function to wrap non-escaped user input
     *
     * @param array $stringCollection
     *
     * @return string
     */
    public static function wrapStringCollection(array $stringCollection)
    {
        $wrappedCollection = [];

        foreach ($stringCollection as $string) {
            $wrapped = self::wrapString($string);

            $wrappedCollection[] = $wrapped;
        }

        return $wrappedCollection;
    }

    /**
     * Returns the given string wrapped in backquotes.
     *
     * @param string $name
     *
     * @return string
     */
    public static function backquoteTableOrColumnName($name)
    {
        return self::doWrap($name, '`');
    }

    /**
     * Returns a copy of the given collection of elements wrapped in backquotes.
     *
     * @param array $arr
     *
     * @return string
     */
    public static function backquoteArrayOfTableOrColumnNames(array $arr)
    {
        foreach ($arr as $key => $name) {
            $arr[$key] = self::backquoteTableOrColumnName($name);
        }

        return $arr;
    }

    /**
     * Gets the foreign key unique name,
     * to be used as the name of the foreign key in the database,
     * from the given ForeignKey instance.
     *
     * @param ForeignKey $foreignKey
     *
     * @return string
     */
    public static function getForeignKeyUniqueNameFromForeignKey(ForeignKey $foreignKey)
    {
        $sha1 = sha1(
            self::getForeignKeyUniqStringIdFromForeignKey($foreignKey)
        );

        return "fk_$sha1";
    }

    /**
     * Gets the foreign key unique name,
     * to be used as the name of the foreign key in the database,
     * from the given foreign key as array.
     *
     * @param array $foreignKeyAsArray
     *
     * @return string
     */
    public static function getForeignKeyUniqueNameFromForeignKeyAsArray(array $foreignKeyAsArray)
    {
        $sha1 = sha1(
            self::getForeignKeyUniqueStringIdFromForeignKeyAsArray($foreignKeyAsArray)
        );

        return "fk_$sha1";
    }

    /**
     * Generates the column SQL part from a given Column instance.
     *
     * @param Column $column
     *
     * @return string
     *
     * @throws PdoMySqlDriverException either if:
     * - the column has no table
     * - the column has no name
     * - the column has no type
     * - the column nullable property hasn't been defined
     * - the column is of type ColumnType::TYPE_ENUM and the allowedValues parameter hasn't been defined
     */
    public static function generateColumnSql(Column $column)
    {
        $columnName = $column->getName();
        $columnType = $column->getType();
        $nullable = $column->isNullable();
        $parameters = $column->getParameters();
        $table = $column->getTable();

        if (!isset($table)) {
            throw new PdoMySqlDriverException('A table is required to generate column SQL.');
        }

        $tableName = $table->getName();

        if (empty($columnName)) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'A column name is required for column from table "%s".',
                    $tableName
                )
            );
        }

        if (empty($columnType)) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'A column type is required for column "%s" from table "%s".',
                    $columnName,
                    $tableName
                )
            );
        }

        if ($nullable === null) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'Nullable is required for column "%s" from table "%s".',
                    $columnName,
                    $tableName
                )
            );
        }

        if ($columnType === ColumnType::TYPE_ENUM
            && !array_key_exists('allowedValues', $parameters)
        ) {
            throw new PdoMySqlDriverException(
                'Parameter allowedValues is required.'
            );
        }

        $columnSql = MySqlHelper::backquoteTableOrColumnName($columnName);

        $columnSql .= " $columnType";

        if ($columnType === ColumnType::TYPE_ENUM) {
            $allowedValues = $parameters['allowedValues'];
            $stringifiedValues = [];

            foreach ($allowedValues as $allowedValue) {
                $stringifiedValues[] = MySqlHelper::wrapString($allowedValue);
            }

            $columnSql .= MySqlHelper::generateSqlCollectionBetweenParentheses($stringifiedValues);
        } elseif (array_key_exists('length', $parameters)) {
            $length = $parameters['length'];
            $columnSql .= MySqlHelper::betweenParentheses($length);
        }

        if (array_key_exists('unsigned', $parameters) && $parameters['unsigned']) {
            $columnSql .= ' UNSIGNED';
        }

        if ($nullable) {
            $columnSql .= ' DEFAULT NULL';
        } else {
            $columnSql .= ' NOT NULL';
        }

        return $columnSql;
    }

    /**
     * Generates the columns' SQL part of the create table statement,
     * wrapped in parentheses.
     *
     * @param array $columns
     *
     * @return string
     */
    public static function generateCreateTableColumnsSql(array $columns)
    {
        $columnsSqlParts = [];

        foreach ($columns as $column) {
            $columnsSqlParts[] = MySqlHelper::generateColumnSql($column);
        }

        $columnsSql = MySqlHelper::generateSqlCollectionBetweenParentheses($columnsSqlParts);

        return $columnsSql;
    }

    /**
     * Generates the full ALTER TABLE statement to create a column
     * from given Column instance.
     *
     * @param Column $column
     *
     * @return string
     */
    public static function generateCreateAlterTableCreateColumnSql(Column $column)
    {
        $tableName = $column
            ->getTable()
            ->getName();

        $sql = 'ALTER TABLE '
            .MySqlHelper::backquoteTableOrColumnName($tableName)
            .' ADD COLUMN '
            .MySqlHelper::generateColumnSql($column);

        return $sql;
    }

    /**
     * Generates the full ALTER TABLE statement to create an index
     * from given Index instance.
     *
     * @param Index $index
     *
     * @return string
     *
     * @throws PdoMySqlDriverException either if:
     * - the index has no column
     * - the index has no name
     * - the index has no columns
     */
    public static function generateCreateAlterTableCreateIndexSql(Index $index)
    {
        $table = $index->getTable();
        $indexName = $index->getName();
        if (!isset($table)) {
            throw new PdoMySqlDriverException(
                'A table is required to generate column SQL.'
            );
        }

        $tableName = $table->getName();

        if (empty($indexName)) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'An index name is required for index from table "%s".',
                    $tableName
                )
            );
        }

        $columns = $index->getColumns();

        if (empty($columns)) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'Index "%s" from table "%s" requires at least one column.',
                    $indexName,
                    $tableName
                )
            );
        }

        $kind = $index->getType();

        $query = 'ALTER TABLE '
            .MySqlHelper::backquoteTableOrColumnName($tableName);

        $query .= ' ADD';

        if ($kind !== null) {
            if ($kind === IndexType::KIND_UNIQUE) {
                $query .= " $kind";
            }
        }

        $query .= ' INDEX ';

        if ($kind === IndexType::KIND_FULLTEXT || $kind === IndexType::KIND_SPATIAL) {
            $query .= "$kind ";
        }

        $query .= MySqlHelper::backquoteTableOrColumnName($indexName);

        $columnsNames = [];

        foreach ($columns as $column) {
            $columnName = $column->getName();
            $columnsNames[] = MySqlHelper::backquoteTableOrColumnName($columnName);
        }

        $query .= ' '
            .MySqlHelper::generateSqlCollectionBetweenParentheses($columnsNames);

        if ($kind === IndexType::BTREE || $kind === IndexType::HASH) {
            $query .= " USING $kind";
        }

        return $query;
    }

    /**
     * Generates the full ALTER TABLE statement to update an existing column
     * to make it autoincrementable.
     *
     * @param Column $destinationColumn
     * @param Table $destinationTable
     *
     * @return string
     */
    public static function generateAlterTableSetAutoIncrementSqlQuery(
        Column $destinationColumn,
        Table $destinationTable
    ) {
        $destinationColumnName = $destinationColumn->getName();
        $destinationTableName = $destinationTable->getName();

        $query = 'ALTER TABLE'
            .' '
            .self::backquoteTableOrColumnName($destinationTableName)
            .' CHANGE '
            .self::backquoteTableOrColumnName($destinationColumnName)
            .' '
            .self::generateColumnSql($destinationColumn)
            .' AUTO_INCREMENT';

        return $query;
    }

    /**
     * Generates the full ALTER TABLE statement to update an existing column
     * to make it non-autoincrementable.
     *
     * @param Column $destinationColumn
     * @param Table $destinationTable
     *
     * @return string
     */
    public static function generateAlterTableUnsetAutoIncrementSqlQuery(
        Column $destinationColumn,
        Table $destinationTable
    ) {
        $destinationColumnName = $destinationColumn->getName();
        $destinationTableName = $destinationTable->getName();

        $query = 'ALTER TABLE'
            .' '
            .self::backquoteTableOrColumnName($destinationTableName)
            .' CHANGE '
            .self::backquoteTableOrColumnName($destinationColumnName)
            .' '
            .self::generateColumnSql($destinationColumn);

        return $query;
    }

    /**
     * Generates a query to start a transaction.
     *
     * @return string
     */
    public static function generateMySqlStartTransactionQuery()
    {
        return 'START TRANSACTION';
    }

    /**
     * Generates a commit query.
     *
     * @return string
     */
    public static function generateMySqlCommitQuery()
    {
        return 'COMMIT';
    }

    /**
     * Whether there's a difference between the two columns
     * and that difference concerns the autoincrement
     *
     * @param Column $column1
     * @param Column $column2
     *
     * @return bool
     */
    public static function columnsAreSameIgnoreAutoIncrement(Column $column1, Column $column2)
    {
        $column1AsArray = $column1->toArray();
        $column2AsArray = $column2->toArray();

        $diff = ArrayHelper::arrayDiffRecursive($column1AsArray, $column2AsArray);
        $diff1 = ArrayHelper::arrayDiffRecursive($column2AsArray, $column1AsArray);

        unset($diff['autoIncrement']);
        unset($diff1['autoIncrement']);

        return empty($diff) && empty($diff1);
    }

    /**
     * Returns the given string wrapped in the given $between.
     *
     * @param string $string the string to be wrapped
     * @param string $between the string to be used to wrap the first parameter
     *
     * @return string the wrapped string
     */
    private static function doWrap($string, $between)
    {
        return $between.$string.$between;
    }

    /**
     * Gets the foreign key unique id base, to be used
     * to create the foreign unique id,
     * from a given foreign key as array.
     *
     * @param array $foreignKeyAsArray
     *
     * @return string
     */
    private static function getForeignKeyUniqueStringIdFromForeignKeyAsArray(array $foreignKeyAsArray)
    {
        $columnsNames = $foreignKeyAsArray['columns'];
        $referencedColumnsNames = $foreignKeyAsArray['referencedColumns'];
        $tableName = $foreignKeyAsArray['table'];
        $referencedTableName = $foreignKeyAsArray['referencedTable'];

        return $tableName
            .'_'
            .implode('_', $columnsNames). '_'
            .$referencedTableName
            .'_'
            .implode('_', $referencedColumnsNames);

    }

    /**
     * Gets the foreign key unique id base, to be used
     * to create the foreign unique id,
     * from a given ForeignKey instance.
     *
     * @param ForeignKey $foreignKey
     *
     * @return string
     */
    private static function getForeignKeyUniqStringIdFromForeignKey(ForeignKey $foreignKey)
    {
        return self::getForeignKeyUniqueStringIdFromForeignKeyAsArray(
            $foreignKey->toArray()
        );
    }
}
