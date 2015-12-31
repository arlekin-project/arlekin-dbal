<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql;

use Arlekin\Dbal\Driver\Pdo\MySql\Log\QueryLoggerInterface;

/**
 * A MySQL database connection which logs its queries.
 * 
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class LoggedDatabaseConnection extends DatabaseConnection
{
    /**
     * @var QueryLoggerInterface
     */
    protected $logger;
    
    /**
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param QueryLoggerInterface $logger
     */
    public function __construct($host, $port, $database, $user, $password, QueryLoggerInterface $logger) {
        $this->logger = $logger;
        
        parent::__construct($host, $port, $database, $user, $password);
    }
    
    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $parameters = [], array $otherParameters = [])
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
    
    public function disconnect()
    {
        parent::disconnect();
        
        $this->logger->end();
    }
}
