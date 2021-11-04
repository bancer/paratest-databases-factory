<?php

declare(strict_types=1);

namespace Bancer\ParatestDatabasesFactory\TestCase;

use PHPUnit\Framework\TestCase;
use Bancer\ParatestDatabasesFactory\DatabasesFactory;

class DatabasesFactoryTest extends TestCase
{
    public function testCreateDatabase()
    {
        $factory = new DatabasesFactory();
        $actual = $factory
            ->setDsn(getenv('pf_dsn'))
            ->setUsername(getenv('pf_user'))
            ->setPassword(getenv('pf_pass'))
            ->createDatabase('pf_temp');
        $this->assertSame(1, $actual->rowCount());
        $this->assertSame('00000', $actual->errorCode());
        $factory->getPdo()->query('DROP DATABASE pf_temp');
    }
}
