<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Exception;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class QueryWithNonEstablishedConnectionException extends DriverException
{
    public function __construct()
    {
        parent::__construct('Trying to execute a query using a non-established connection.');
    }
}
