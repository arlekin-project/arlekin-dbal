<?php

namespace Arlekin\Dbal\Helper\Dump;

class RawStringValue
{
    protected $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function __toString()
    {
        return (string)$this->value;
    }
}
