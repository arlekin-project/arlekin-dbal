<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log;

class LogEntry
{
    /**
     * @var string
     */
    protected $query;
    
    /**
     * @var array
     */
    protected $parameters;
    
    /**
     * @var float
     */
    protected $start;
    
    /**
     * @var float
     */
    protected $end;
    
    /**
     * @var float
     */
    protected $duration;
    
    /**
     * @var mixed
     */
    protected $payload;
    
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
