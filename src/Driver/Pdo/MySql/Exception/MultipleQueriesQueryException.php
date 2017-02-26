<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Exception;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class MultipleQueriesQueryException extends DriverException
{
    /**
     * @param string $query
     * @param \Exception|null $previous
     */
    public function __construct(string $query, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'Error executing query: %s',
                $query
            ),
            0,
            $previous
        );
    }
}
