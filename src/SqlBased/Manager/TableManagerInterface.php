<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlecchino\DatabaseAbstractionLayer\SqlBased\Manager;

use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Column;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Index;
use Arlecchino\DatabaseAbstractionLayer\SqlBased\Element\Table;
use Exception;

/**
 * To manage SQL-based tables.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
interface TableManagerInterface
{
    /**
     * Removes a column with given name from given Schema.
     * Note that the column has to exists.
     *
     * @param Table $table
     * @param string $columnName
     *
     * @return TableManagerInterface
     *
     * @throws Exception if no column is found for given column name.
     */
    public function removeColumnWithName(
        Table $table,
        $columnName
    );

    /**
     * Removes an index with given name from given Schema.
     * Note that the index has to exists.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return TableManagerInterface
     *
     * @throws Exception if no index is found for given index name.
     */
    public function removeIndexWithName(
        Table $table,
        $indexName
    );

    /**
     * Removes a foreign key with given column names,
     * referenced table name and referenced columns name as identifiers
     * from given table.
     * Note that the foreign key has to exists.
     *
     * @param Table $table
     * @param array $columnsNames
     * @param string $referencedTableName
     * @param array $referencedColumnsNames
     *
     * @return TableManagerInterface
     *
     * @throws Exception if no foreign key is found.
     */
    public function removeForeignKeyWithColumnsAndReferencedColumnsNamed(
        Table $table,
        array $columnsNames,
        $referencedTableName,
        array $referencedColumnsNames
    );

    /**
     * Whether a foreign key with given column names,
     * referenced table name and referenced columns name as identifiers
     * exists in given table.
     *
     * @param Table $table
     * @param array $columnsNames
     * @param string $referencedTableName
     * @param array $referencedColumnsNames
     *
     * @return boolean
     */
    public function hasForeignKeyWithColumnsAndReferencedColumnsNamed(
        Table $table,
        array $columnsNames,
        $referencedTableName,
        array $referencedColumnsNames
    );

    /**
     * Whether the given table has the given column.
     *
     * @param Table $table
     * @param Column $column
     *
     * @return bool
     */
    public function hasColumn(
        Table $table,
        Column $column
    );

    /**
     * Whether the given table has a column with given name.
     *
     * @param Table $table
     * @param string $columnName
     *
     * @return bool
     */
    public function hasColumnWithName(
        Table $table,
        $columnName
    );

    /**
     * Whether the given table has an index with given name.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return bool
     */
    public function hasIndexWithName(
        Table $table,
        $indexName
    );

    /**
     * Whether the given table has a primary key
     * which columns names are identical to the given column names.
     *
     * @param Table $table
     * @param array $columnNames
     *
     * @return bool
     */
    public function hasPrimaryKeyWithColumnsNamed(
        Table $table,
        array $columnNames
    );

    /**
     * Gets an index with given name from given table.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return Index
     *
     * @throws Exception if no index with given name is found
     */
    public function getIndexWithName(
        Table $table,
        $indexName
    );

    /**
     * Gets a column with given name from given table.
     *
     * @param Table $table
     * @param string $columnName
     *
     * @return Column
     *
     * @throws Exception
     */
    public function getColumnWithName(
        Table $table,
        $columnName
    );

    /**
     * Whether there's a difference between the two columns
     * and that difference concerns the autoincrement
     *
     * @param Column $column1
     * @param Column $column2
     *
     * @return bool
     */
    public function columnsAreSameIgnoreAutoIncrement(
        Column $column1,
        Column $column2
    );
}
