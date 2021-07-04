<?php

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
