<?php

namespace Jefhar\tests;

use PHPUnit\Framework\TestCase;

class InitialTest extends TestCase
{
    /**
     * Initial Test to make sure phpunit is configured correctly.
     * @test
     * @return void
     */
    public function oneForFree(): void
    {
        $this->assertTrue(true);
    }
}
