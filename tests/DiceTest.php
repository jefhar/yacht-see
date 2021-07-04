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

use Jefhar\Yachtsee\Dice;
use Jefhar\Yachtsee\Exceptions\UnrolledDieException;
use PHPUnit\Framework\TestCase;

class DiceTest extends TestCase
{
    /**
     * @test
     */
    public function aDieHasARoll(): void
    {
        $die = new Dice();
        $die->roll();
        self::assertIsInt($die->getNumber());
        self::assertGreaterThanOrEqual(1, $die->getNumber());
        self::assertLessThanOrEqual(6, $die->getNumber());
    }

    /**
     * @test
     */
    public function aDieCanBeFrozen(): void
    {
        $die = new Dice();
        $die->roll();
        $number = $die->getNumber();
        $die->freeze();
        self::assertEquals($number, $die->getNumber());
        self::assertGreaterThanOrEqual(1, $die->getNumber());
        self::assertLessThanOrEqual(6, $die->getNumber());
    }

    /**
     * @test
     */
    public function anUnrolledDieGetNumberThrowsException(): void
    {
        $this->expectException(UnrolledDieException::class);
        $die = new Dice();
        $die->freeze();
        $die->roll();
        $die->getNumber();
    }

    /**
     * @test
     */
    public function aDieCanBeUnfrozen(): void
    {
        $die = new Dice();
        $die->freeze();
        $die->roll();
        $die->unfreeze();
        $die->roll();
        self::assertGreaterThanOrEqual(1, $die->getNumber());
        self::assertLessThanOrEqual(6, $die->getNumber());
    }
}
