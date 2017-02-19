<?php

namespace Calam\Dbal\Driver\Pdo\MySql\Log;

class LogEntry
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var float
     */
    private $start;

    /**
     * @var float
     */
    private $end;

    /**
     * @var float
     */
    private $duration;

    /**
     * @var mixed
     */
    private $payload;

    public function __construct($query, $parameters, $start, $end, $payload) {
        $this->query = $query;
        $this->parameters = $parameters;
        $this->start = $start;
        $this->end = $end;
        $this->duration = $end - $start;
        $this->payload = $payload;
    }

    public function toArray()
    {
        return [
            'query' => $this->query,
            'parameters' => $this->parameters,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'payload' => $this->payload,
        ];
    }
}
