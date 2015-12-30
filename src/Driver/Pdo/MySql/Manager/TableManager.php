<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql\Manager;

use Arlekin\Dbal\Helper\ArrayHelper;
use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Arlekin\Dbal\Driver\Pdo\MySql\Helper\MySqlHelper;
use Arlekin\Dbal\SqlBased\Element\Column;
use Arlekin\Dbal\SqlBased\Element\Table;
use Arlekin\Dbal\SqlBased\Manager\TableManagerInterface;

/**
 * To manage MySQL tables.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableManager implements TableManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeColumnWithName(Table $table, $name)
    {
        foreach ($table->getColumns() as $i => $column) {
            if ($column->getName() === $name) {
                $table->removeColumnAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table has no column with name "%s".',
                $name
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeIndexWithName(Table $table, $name)
    {
        foreach ($table->getIndexes() as $i => $index) {
            if ($index->getName() === $name) {
                $table->removeIndexAtIndex($i);

                return $this;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table has no index with name "%s".',
                $name
            )
        );
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function hasColumn(Table $table, Column $column)
    {
        return in_array(
            $column,
            $table->getColumns()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnWithName(Table $table, $name)
    {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIndexWithName(Table $table, $name)
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getIndexWithName(Table $table, $name)
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->getName() === $name) {
                return $index;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no index with name "%s".',
                $table->getName(),
                $name
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnWithName(Table $table, $name)
    {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

        throw new PdoMySqlDriverException(
            sprintf(
                'Table "%s" has no column with name "%s".',
                $table->getName(),
                $name
            )
        );
    }

    /**
     * {@inheritdoc}
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
