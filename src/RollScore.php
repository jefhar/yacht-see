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

namespace Jefhar\Yachtsee;

/**
 * Class RollScore
 */
class RollScore
{
    public const SCORE_FULL_HOUSE = 25;
    public const SCORE_LARGE_STRAIGHT = 40;
    public const SCORE_SMALL_STRAIGHT = 30;
    public const SCORE_YACHTSEE = 50;

    /**
     * @var Dice[]
     */
    private array $dice;

    /**
     * RollScore constructor.
     * @param Dice[] $dice
     */
    public function __construct(array $dice)
    {
        $this->dice = $dice;
    }

    /**
     * @return int[]
     * @throws Exceptions\UnrolledDieException
     */
    private function getDieCounts(): array
    {
        $counts = [1 => 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i < 5; ++$i) {
            $counts[$this->dice[$i]->getNumber()]++;
        }

        return $counts;
    }

    /**
     * Counts number of dice with a particular digit and multiplies by that digit.
     *
     * @param int $digit
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    private function getDigitScore(int $digit): int
    {
        $counts = $this->getDieCounts();

        return $counts[$digit] * $digit;
    }

    /**
     * Scores a chance: Sum of all dice. Same scoring for 3- and 4-of a kind.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreChance(): int
    {
        /** @var int[] $pips */
        $pips = [];
        for ($i = 0; $i < 5; ++$i) {
            $pips[] = $this->dice[$i]->getNumber();
        }

        return array_sum($pips);
    }

    /**
     * @param int $n
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    private function isNOfAKind(int $n): bool
    {
        $count = $this->getDieCounts();
        $nKind = array_filter($count, static fn($v, $k) => $v >= $n, ARRAY_FILTER_USE_BOTH);

        return count($nKind) === 1;
    }

    /**
     * @param int $n
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    private function isExactNOfAKind(int $n): bool
    {
        $count = $this->getDieCounts();
        $nKind = array_filter($count, static fn($v, $k) => $v === $n, ARRAY_FILTER_USE_BOTH);

        return count($nKind) === 1;
    }

    /**
     * Returns score for Aces. Add up the number of aces and multiply by 1.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreAces(): int
    {
        return $this->getDigitScore(1);
    }

    /**
     * Returns score for Twos. Add up the number of 2s and multiply by 2.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreTwos(): int
    {
        return $this->getDigitScore(2);
    }

    /**
     * Returns score for Threes.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreThrees(): int
    {
        return $this->getDigitScore(3);
    }

    /**
     * Returns score for Fours
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreFours(): int
    {
        return $this->getDigitScore(4);
    }

    /**
     * Returns score for Fives
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreFives(): int
    {
        return $this->getDigitScore(5);
    }

    /**
     * Returns score for Sixes
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreSixes(): int
    {
        return $this->getDigitScore(6);
    }

    /**
     * Whether dice contain a three of a kind.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isTrips(): bool
    {
        return $this->isNOfAKind(3);
    }

    /**
     * Whether dice contain exactly three of a kind. Fails
     * if the dice contain a four- or five-of a kind.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    private function isExactTrips(): bool
    {
        return $this->isExactNOfAKind(3);
    }

    /**
     * Whether dice contain an exact pair.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isExactPair(): bool
    {
        return $this->isExactNOfAKind(2);
    }

    /**
     * Returns score for Three-Of-A-Kind.
     *
     * If roll has a three of a kind, score is sum of all dice.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreTrips(): int
    {
        return (int)$this->isTrips() * $this->scoreChance();
    }

    /**
     * Whether dice contain a four of a kind.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isQuads(): bool
    {
        return $this->isNOfAKind(4);
    }

    /**
     * Returns score for Four-Of-A-Kind.
     *
     * If roll has a four of a kind, score is sum of all dice.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreQuads(): int
    {
        return (int)$this->isQuads() * $this->scoreChance();
    }

    /**
     * Whether dice contain a full house. 3 of a kind + a pair,
     * or a YachtSee.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isFullHouse(): bool
    {
        return ($this->isExactTrips() && $this->isExactPair()) || $this->isYachtSee();
    }

    /**
     * Returns score for a Full House
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreFullHouse(): int
    {
        return (int)$this->isFullHouse() * self::SCORE_FULL_HOUSE;
    }

    /**
     * Whether dice contain a small straight. 4 pips in a row.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isSmallStraight(): bool
    {
        $count = $this->getDieCounts();
        // Must have a 3 and a 4.
        // And one of the following combinations:
        // 1 and 2
        // 2 and 5
        // 5 and 6
        return ($count[3] && $count[4]) &&
            (
                ($count[1] && $count[2]) ||
                ($count[2] && $count[5]) ||
                ($count[5] && $count[6])
            );
    }

    /**
     * Return score for a Small Straight.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreSmallStraight(): int
    {
        return (int)$this->isSmallStraight() * self::SCORE_SMALL_STRAIGHT;
    }

    /**
     * Whether dice contain a Large Straight. 5 pips in a row.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isLargeStraight(): bool
    {
        $count = $this->getDieCounts();

        return ($count[2] && $count[3] && $count[4] && $count[5]) &&
            ($count[1] || $count[6]);
    }

    /**
     * Returns score for a Large Straight
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreLargeStraight(): int
    {
        return (int)$this->isLargeStraight() * self::SCORE_LARGE_STRAIGHT;
    }

    /**
     * Whether dice contain a five of a kind.
     *
     * @return bool
     * @throws Exceptions\UnrolledDieException
     */
    public function isYachtSee(): bool
    {
        return $this->isNOfAKind(5);
    }

    /**
     * Returns score for five-Of-A-Kind.
     *
     * If roll has a five of a kind, score is 50.
     *
     * @return int
     * @throws Exceptions\UnrolledDieException
     */
    public function scoreYachtSee(): int
    {
        return (int)$this->isYachtSee() * self::SCORE_YACHTSEE;
    }
}
