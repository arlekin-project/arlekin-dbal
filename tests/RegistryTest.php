<?php

namespace Arlekin\Dbal\Test;

use Arlekin\Dbal\Manager\DatabaseConnectionManager;
use Arlekin\Dbal\Registry;
use PHPUnit_Framework_TestCase;

class RegistryTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $registry = new Registry();
        
        $this->assertAttributeInternalType('array', 'driversByName', $registry);
        $this->assertAttributeInstanceOf(DatabaseConnectionManager::class, 'databaseConnectionManager', $registry);
    }
}