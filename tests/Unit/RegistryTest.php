<?php

namespace Arlekin\Dbal\Tests\Unit;

use Arlekin\Dbal\Manager\DatabaseConnectionManager;
use Arlekin\Dbal\Registry;
use Arlekin\Dbal\Tests\AbstractBaseTest;

class RegistryTest extends AbstractBaseTest
{
    public function testConstruct()
    {
        $registry = new Registry();
        
        $this->assertAttributeInternalType('array', 'driversByName', $registry);
        $this->assertAttributeInstanceOf(DatabaseConnectionManager::class, 'databaseConnectionManager', $registry);
    }
}