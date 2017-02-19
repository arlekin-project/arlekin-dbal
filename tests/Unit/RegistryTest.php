<?php

namespace Calam\Dbal\Tests\Unit;

use Calam\Dbal\Manager\DatabaseConnectionManager;
use Calam\Dbal\Registry;
use Calam\Dbal\Tests\BaseTest;

class RegistryTest extends BaseTest
{
    public function testConstruct()
    {
        $registry = new Registry();
        
        $this->assertAttributeInternalType('array', 'driversByName', $registry);
        $this->assertAttributeInstanceOf(DatabaseConnectionManager::class, 'databaseConnectionManager', $registry);
    }
}