<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

use Arlekin\Dbal\Driver\Pdo\MySql\Element\Schema;

class QueryAnalysisResult
{
    /**
     * @var Schema
     */
    protected $schema;
    
    /**
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }
    
    public function getSchema() {
        return $this->schema;
    }
}
