<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Manager;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Column;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Index;
use Arlekin\Dbal\Driver\Pdo\MySql\Element\Table;
use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Arlekin\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper;
use Arlekin\Dbal\Exception\DbalException;
use Arlekin\Dbal\Helper\ArrayHelper;

/**
 * To manage MySQL tables.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableManager
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
     * @throws DbalException if no column is found for given column name.
     */
    public function removeColumnWithName(Table $table, $columnName)
    {
        foreach ($table->getColumns() as $i => $column) {
            if ($column->getName() === $columnName) {
                $table->removeColumnAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table has no column with name "%s".',
                $columnName
            )
        );
    }

    /**
     * Removes an index with given name from given Schema.
     * Note that the index has to exists.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return TableManagerInterface
     *
     * @throws DbalException if no index is found for given index name.
     */
    public function removeIndexWithName(Table $table, $indexName)
    {
        foreach ($table->getIndexes() as $i => $index) {
            if ($index->getName() === $indexName) {
                $table->removeIndexAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table has no index with name "%s".',
                $indexName
            )
        );
    }

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
     * @throws DbalException if no foreign key is found.
     */
    public function removeForeignKeyWithColumnsAndReferencedColumnsNamed(
        Table $table,
        array $columnsNames,
        $referencedTableName,
        array $referencedColumnsNames
    ) {
        $foreignKeyAsArray = [
            'table' => $table->getName(),
            'columns' => $columnsNames,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsNames,
        ];

        $foreignKeyToRemoveHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray($foreignKeyAsArray);

        $foreignKeyToRemoveIndex = null;
        $foreignKeys = $table->getForeignKeys();

        foreach ($foreignKeys as $key => $tableForeignKey) {
            $tableForeignKeyHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKey($tableForeignKey);

            if ($tableForeignKeyHash === $foreignKeyToRemoveHash) {
                $foreignKeyToRemoveIndex = $key;
            }
        }

        if ($foreignKeyToRemoveIndex === null) {
            throw new PdoMySqlDriverException(
                sprintf(
                    'Table has no foreign key like %s.',
                    json_encode($foreignKeyAsArray)
                )
            );
        }

        $table->removeForeignKeyAtIndex($foreignKeyToRemoveIndex);

        return $this;
    }

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
    ) {
        $foreignKeyAsArray = [
            'table' => $table->getName(),
            'columns' => $columnsNames,
            'referencedTable' => $referencedTableName,
            'referencedColumns' => $referencedColumnsNames,
        ];

        $foreignKeyToRemoveHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKeyAsArray($foreignKeyAsArray);

        $has = false;
        $foreignKeys = $table->getForeignKeys();

        foreach ($foreignKeys as $tableForeignKey) {
            $tableForeignKeyHash = MySqlHelper::getForeignKeyUniqueNameFromForeignKey($tableForeignKey);

            if ($tableForeignKeyHash === $foreignKeyToRemoveHash) {
                $has = true;
            }
        }

        return $has;
    }

    /**
     * Whether the given table has the given column.
     *
     * @param Table $table
     * @param Column $column
     *
     * @return bool
     */
    public function hasColumn(Table $table, Column $column)
    {
        return in_array(
            $column,
            $table->getColumns()
        );
    }

    /**
     * Whether the given table has a column with given name.
     *
     * @param Table $table
     * @param string $columnName
     *
     * @return bool
     */
    public function hasColumnWithName(Table $table, $columnName)
    {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the given table has an index with given name.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return bool
     */
    public function hasIndexWithName(Table $table, $indexName)
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the given table has a primary key
     * which columns names are identical to the given column names.
     *
     * @param Table $table
     * @param array $columnNames
     *
     * @return bool
     */
    public function hasPrimaryKeyWithColumnsNamed(Table $table, array $columnNames)
    {
        $primaryKey = $table->getPrimaryKey();

        if ($primaryKey === null) {
            $hasPrimaryKeyWithColumnsNamed = false;
        } else {
            $primaryKeyAsArray = $primaryKey->toArray();

            $hasPrimaryKeyWithColumnsNamed = array_values(
                $primaryKeyAsArray['columns']
            ) === array_values(
                $columnNames
            );
        }

        return $hasPrimaryKeyWithColumnsNamed;
    }

    /**
     * Gets an index with given name from given table.
     *
     * @param Table $table
     * @param string $indexName
     *
     * @return Index
     *
     * @throws DbalException if no index with given name is found
     */
    public function getIndexWithName(Table $table, $indexName)
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->getName() === $indexName) {
                return $index;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no index with name "%s".',
                $table->getName(),
                $indexName
            )
        );
    }

    /**
     * Gets a column with given name from given table.
     *
     * @param Table $table
     * @param string $columnName
     *
     * @return Column
     *
     * @throws DbalException
     */
    public function getColumnWithName(Table $table, $columnName)
    {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === $columnName) {
                return $column;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no column with name "%s".',
                $table->getName(),
                $columnName
            )
        );
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
    public function columnsAreSameIgnoreAutoIncrement(Column $column1, Column $column2)
    {
        $column1AsArray = $column1->toArray();
        $column2AsArray = $column2->toArray();

        $diff = ArrayHelper::arrayDiffRecursive($column1AsArray, $column2AsArray);
        $diff1 = ArrayHelper::arrayDiffRecursive($column2AsArray, $column1AsArray);

        unset($diff['autoIncrement']);
        unset($diff1['autoIncrement']);

        return empty($diff) && empty($diff1);
    }
}
