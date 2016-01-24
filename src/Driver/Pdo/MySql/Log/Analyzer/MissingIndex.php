<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class MissingIndex
{
    /**
     * @var array
     */
    private $columns;

    public function getColumns() {
        return $this->columns;
    }

    public function setColumns($columns) {
        $this->columns = $columns;

        return $this;
    }
}