<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class ObjectQueryAnalyzeResult
{
    /**
     * @var ObjectQuery
     */
    private $query;

    /**
     * @param ObjectQuery $query
     */
    public function __construct(ObjectQuery $query)
    {
        $this->query = $query;
    }

    /**
     * @return ObjectQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
