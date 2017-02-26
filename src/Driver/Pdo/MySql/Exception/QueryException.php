<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Exception;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class QueryException extends DriverException
{
    /**
     * @var string
     */
    private $sqlStateErrorCode;

    /**
     * @var string
     */
    private $driverSpecificErrorCode;

    /**
     * @var string
     */
    private $driverSpecificErrorMessage;

    /**
     * @param string $sqlStateErrorCode
     * @param string $driverSpecificErrorCode
     * @param string $driverSpecificErrorMessage
     */
    public function __construct(
        string $sqlStateErrorCode,
        string $driverSpecificErrorCode,
        string $driverSpecificErrorMessage
    ) {
        $this->sqlStateErrorCode = $sqlStateErrorCode;
        $this->driverSpecificErrorCode = $driverSpecificErrorCode;
        $this->driverSpecificErrorMessage = $driverSpecificErrorMessage;

        parent::__construct(
            sprintf(
                'Error querying: SQLSTATE error code %s / MySQL error code %s: %s',
                $sqlStateErrorCode,
                $driverSpecificErrorCode,
                $driverSpecificErrorMessage
            )
        );
    }
}
