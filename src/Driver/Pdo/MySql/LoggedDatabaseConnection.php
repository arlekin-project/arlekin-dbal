<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql;

use Calam\Dbal\Driver\Pdo\MySql\Log\QueryLoggerInterface;

/**
 * MySQL database connection which logs queries and performance.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
final class LoggedDatabaseConnection extends DatabaseConnection
{
    /**
     * @var QueryLoggerInterface
     */
    private $logger;

    /**
     * @param QueryLoggerInterface $logger
     * @param string $host
     * @param int|null $port
     * @param string $database
     * @param string $user
     * @param string|null $password
     * @param array $options
     */
    public function __construct(
        QueryLoggerInterface $logger,
        string $host,
        ?int $port,
        string $database,
        string $user,
        ?string $password,
        array $options = []
    ) {
        $this->logger = $logger;

        parent::__construct($host, $port, $database, $user, $password, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery(string $query, array $parameters = []): array
    {
        $start = microtime(true);

        $result = parent::executeQuery($query, $parameters);

        $end = microtime(true);

        if (isset($otherParameters['log']['payload'])) {
            $logPayload = $otherParameters['log']['payload'];
        } else {
            $logPayload = null;
        }

        $this->logger->log($query, $parameters, $start, $end, $logPayload);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): DatabaseConnection
    {
        parent::disconnect();

        $this->logger->end();

        return $this;
    }
}
