<?php

/*
 * Copyright (c) 2021 Jeff Harris <jeff@jeffharris.us>.
 * This work is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */
declare(strict_types=1);

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
