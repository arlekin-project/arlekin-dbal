<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arlekin\Dbal\Driver\Pdo\MySql;

use Arlekin\Dbal\Driver\Pdo\MySql\Exception\PdoMySqlDriverException;
use Arlekin\Dbal\SqlBased\DatabaseConnectionInterface;

/**
 * Represents a MySQL database connection.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class DatabaseConnection implements DatabaseConnectionInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * Constructor.
     *
     * @param string $host
     * @param integer $port
     * @param string $database
     * @param string $user
     * @param string $password
     */
    public function __construct($host, $port, $database, $user, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return $this->connection !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            throw new PdoMySqlDriverException('Connection already established.');
        }

        $dsn = sprintf(
            'mysql:dbname=%s;host=%s',
            $this->database,
            $this->host
        );

        if (!empty($this->port)) {
            $dsn .= sprintf(
                ';port=%s',
                $this->port
            );
        }

        $options = [];

        $this->connection = new \PDO($dsn, $this->user, $this->password, $options);

        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 1);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function connectIfNotConnected()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if (!$this->isConnected()) {
            throw new PdoMySqlDriverException('Connection already closed.');
        }
        $this->connection = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $parameters = [])
    {
        if ($this->connection === null) {
            throw new PdoMySqlDriverException('Trying to execute a query using a non-connected connection.');
        }
        
        $arrayParametersToReplace = [];
        $replaceArrayParametersWith = [];

        foreach ($parameters as $parameterName => $parameter) {
            if (is_array($parameter)) {
                $countParameters = count($parameter);
                
                $arrayParametersToReplace[] = "(:{$parameterName})";
                
                $prefixedParameters = [];
                
                for ($i = 0; $i < $countParameters; $i += 1) {
                    $replacementParameterName = "{$parameterName}{$i}";
                    
                    $parameters[$replacementParameterName] = $parameter[$i];
                    
                    $prefixedParameters[] = ":{$replacementParameterName}";
                }
                
                $replaceArrayParametersWith[] = '('.implode(', ', $prefixedParameters).')';
                
                unset($prefixedParameters);
                
                unset($parameters[$parameterName]);
            }
        }
        
        unset($parameterName, $parameter, $countParameters);
        
        if (empty($arrayParametersToReplace)) {
            $replacedQuery = $query;
        } else {
            $replacedQuery = str_replace(
                $arrayParametersToReplace,
                $replaceArrayParametersWith,
                $query
            );
        }

        $preparedStatement = $this->connection->prepare($replacedQuery);

        $executeResult = $preparedStatement->execute($parameters);

        if (!$executeResult) {
            $errorInfo = $preparedStatement->errorInfo();
            $sqlStateErrorError = $errorInfo[0];
            $driverSpecificError = $errorInfo[1];
            $driverSpecificMessage = $errorInfo[2];

            throw new PdoMySqlDriverException(
                sprintf(
                    'Error querying: SQLSTATE error code %s'
                    ." / MySQL error code %s"
                    .": %s",
                    $sqlStateErrorError,
                    $driverSpecificError,
                    $driverSpecificMessage
                )
            );
        }

        $rawRows = [];

        while ($rawRow = $preparedStatement->fetch(\PDO::FETCH_ASSOC)) {
            $rawRows[] = $rawRow;
        }

        $preparedStatement->closeCursor();

        return $rawRows;
    }

    /**
     * {@inheritdoc}
     */
    public function executeMultipleQueries(array $queries)
    {
        $resultSets = [];

        foreach ($queries as $query) {
            try {
                if (is_string($query)) {
                    $resultSet = $this->executeQuery($query);
                } else {
                    $resultSet = $this->executeQuery($query[0], $query[1]);
                }                

                $resultSets[] = $resultSet;
            } catch (\Exception $ex) {
                throw new PdoMySqlDriverException(
                    sprintf(
                        'Error executing query: %s',
                        (string)$query
                    ),
                    null,
                    $ex
                );
            }
        }

        return $resultSets;
    }

    /**
     * Drops all the tables from the database.
     *
     * @return DatabaseConnection the current DatabaseConnection instance
     */
    public function dropAllTables()
    {
        $queries =  [
            'SET FOREIGN_KEY_CHECKS = 0',
            'SET GROUP_CONCAT_MAX_LEN=32768',
            'SET @tables = NULL',
            'SELECT GROUP_CONCAT(table_name) INTO @tables
                    FROM information_schema.tables
                    WHERE table_schema = (SELECT DATABASE()) AND table_type = \'BASE TABLE\'',
            'SELECT IFNULL(@tables,\'dummy\') INTO @tables',
            'SET @tables = CONCAT(\'DROP TABLE IF EXISTS \', @tables)',
            'PREPARE stmt FROM @tables',
            'EXECUTE stmt',
            'DEALLOCATE PREPARE stmt',
            'SET FOREIGN_KEY_CHECKS = 1',
        ];

        $this->executeMultipleQueries($queries);

        return $this;
    }

    /**
     * Drops all the views from the database.
     *
     * @return DatabaseConnection the current DatabaseConnection instance
     */
    public function dropAllViews()
    {
        $queries = [
            'SET FOREIGN_KEY_CHECKS = 0',
            'SET GROUP_CONCAT_MAX_LEN=32768',
            'SET @tables = NULL',
            'SELECT GROUP_CONCAT(table_name) INTO @tables
                    FROM information_schema.tables
                    WHERE table_schema = (SELECT DATABASE()) AND table_type = \'VIEW\'',
            'SELECT IFNULL(@tables,\'dummy\') INTO @tables',
            'SET @tables = CONCAT(\'DROP VIEW IF EXISTS \', @tables)',
            'PREPARE stmt FROM @tables',
            'EXECUTE stmt',
            'DEALLOCATE PREPARE stmt',
            'SET FOREIGN_KEY_CHECKS = 1',
        ];

        $this->executeMultipleQueries($queries);

        return $this;
    }

    /**
     * Drops all the views and tables from the database.
     *
     * @return DatabaseConnection the current DatabaseConnection instance
     */
    public function dropAllDatabaseStructure()
    {
        $this->dropAllViews()
            ->dropAllTables();

        return $this;
    }
}
