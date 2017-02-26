<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Driver\Pdo\MySql;

use Calam\Dbal\Driver\Pdo\MySql\Exception\ConnectionAlreadyClosedException;
use Calam\Dbal\Driver\Pdo\MySql\Exception\ConnectionAlreadyEstablishedException;
use Calam\Dbal\Driver\Pdo\MySql\Exception\MultipleQueriesQueryException;
use Calam\Dbal\Driver\Pdo\MySql\Exception\QueryException;
use Calam\Dbal\Driver\Pdo\MySql\Exception\QueryWithNonEstablishedConnectionException;

/**
 * MySQL database connection.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class DatabaseConnection
{
    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int|null $port
     * @param string $database
     * @param string $user
     * @param string|null $password
     * @param array $options
     */
    public function __construct(
        string $host,
        ?int $port,
        string $database,
        string $user,
        ?string $password,
        array $options = []
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;

        $defaultOptions = [
            \PDO::ATTR_TIMEOUT => 2,
        ];

        $this->options = array_merge($defaultOptions, $options);
    }

    /**
     * True if connected to the database, false otherwise.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connection !== null;
    }

    /**
     * Connects with the database.
     *
     * @throws ConnectionAlreadyEstablishedException if already connected
     *
     * @return DatabaseConnection
     */
    public function connect(): DatabaseConnection
    {
        if ($this->isConnected()) {
            throw new ConnectionAlreadyEstablishedException();
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

        $this->connection = new \PDO($dsn, $this->user, $this->password, $this->options);

        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 1);

        return $this;
    }

    /**
     * Connects with the database if not already connected.
     *
     * It should not fail if already connected, as it does check
     * if already connected before trying to connect.
     *
     * @return DatabaseConnection
     */
    public function connectIfNotConnected(): DatabaseConnection
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        return $this;
    }

    /**
     * Disconnects from the database.
     *
     * @throws ConnectionAlreadyClosedException if already disconnected
     *
     * @return DatabaseConnection
     */
    public function disconnect(): DatabaseConnection
    {
        if (!$this->isConnected()) {
            throw new ConnectionAlreadyClosedException();
        }

        $this->connection = null;

        return $this;
    }

    /**
     * @param string $query
     * @param array $queryParameters
     *
     * @return array
     *
     * @throws QueryWithNonEstablishedConnectionException
     * @throws QueryException
     */
    public function executeQuery(string $query, array $queryParameters = []): array
    {
        if ($this->connection === null) {
            throw new QueryWithNonEstablishedConnectionException();
        }

        $arrayParametersToReplace = [];
        $replaceArrayParametersWith = [];

        foreach ($queryParameters as $parameterName => $parameter) {
            if (is_array($parameter)) {
                $countParameters = count($parameter);

                $arrayParametersToReplace[] = "(:{$parameterName})";

                $prefixedParameters = [];

                for ($i = 0; $i < $countParameters; $i += 1) {
                    $replacementParameterName = "{$parameterName}{$i}";

                    $queryParameters[$replacementParameterName] = $parameter[$i];

                    $prefixedParameters[] = ":{$replacementParameterName}";
                }

                $replaceArrayParametersWith[] = '('.implode(', ', $prefixedParameters).')';

                unset($prefixedParameters);

                unset($queryParameters[$parameterName]);
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

        $executeResult = $preparedStatement->execute($queryParameters);

        if (!$executeResult) {
            $errorInfo = $preparedStatement->errorInfo();
            $sqlStateErrorCode = $errorInfo[0];
            $driverSpecificErrorCode = $errorInfo[1];
            $driverSpecificErrorMessage = $errorInfo[2];

            throw new QueryException($sqlStateErrorCode, $driverSpecificErrorCode, $driverSpecificErrorMessage);
        }

        $rawRows = [];

        while ($rawRow = $preparedStatement->fetch(\PDO::FETCH_ASSOC)) {
            $rawRows[] = $rawRow;
        }

        $preparedStatement->closeCursor();

        return $rawRows;
    }

    /**
     * @param array $queries
     *
     * @return array
     *
     * @throws MultipleQueriesQueryException
     */
    public function executeMultipleQueries(array $queries): array
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
                throw new MultipleQueriesQueryException($query, $ex);
            }
        }

        return $resultSets;
    }

    /**
     * Drops all the tables from the database.
     *
     * @return DatabaseConnection
     */
    public function dropAllTables(): DatabaseConnection
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
     * @return DatabaseConnection
     */
    public function dropAllViews(): DatabaseConnection
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
     * @return DatabaseConnection
     */
    public function dropAllDatabaseStructure(): DatabaseConnection
    {
        $this
            ->dropAllViews()
            ->dropAllTables()
        ;

        return $this;
    }
}
