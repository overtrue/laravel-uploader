<?php

namespace Tests;

use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function teardown(): void
    {
        parent::tearDown();

        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();
    }
}
