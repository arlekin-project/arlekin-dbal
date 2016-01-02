<?php

namespace Arlekin\Dbal\Tests\Unit;

use Arlekin\Dbal\Manager\DatabaseConnectionManager;
use Arlekin\Dbal\Registry;
use Arlekin\Dbal\Tests\BaseTest;

class RegistryTest extends BaseTest
{
    public function testConstruct()
    {
        $registry = new Registry();
        
        $this->assertAttributeInternalType('array', 'driversByName', $registry);
        $this->assertAttributeInstanceOf(DatabaseConnectionManager::class, 'databaseConnectionManager', $registry);
    }
}