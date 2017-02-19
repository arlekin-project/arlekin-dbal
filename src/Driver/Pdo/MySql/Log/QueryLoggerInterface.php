<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Log;

interface QueryLoggerInterface
{
    /**
     * @param string $query
     * @param array $parameters
     * @param float $start
     * @param float $end
     * @param mixed $logPayload
     */
    public function log($query, array $parameters, $start, $end, $logPayload);
    
    public function end();
}
