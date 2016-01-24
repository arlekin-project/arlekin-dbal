<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log\Analyzer;

class MissingIndexAnalyzeResult
{
    /**
     * @var array
     */
    private $missingIndexes;

    public function getMissingIndexes()
    {
        return $this->missingIndexes;
    }

    public function setMissingIndexes($missingIndexes)
    {
        $this->missingIndexes = $missingIndexes;
        
        return $this;
    }
}