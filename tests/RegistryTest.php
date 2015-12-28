<?php

namespace Arlekin\DatabaseAbstractionLayer\Test;

use Arlekin\DatabaseAbstractionLayer\Manager\DatabaseConnectionManager;
use Arlekin\DatabaseAbstractionLayer\Registry;
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