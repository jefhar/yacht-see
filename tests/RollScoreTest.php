<?php

namespace Jefhar\tests;

use Jefhar\Yachtsee\Dice;
use Jefhar\Yachtsee\RollScore;
use PHPUnit\Framework\TestCase;

class RollScoreTest extends TestCase
{
    /**
     * @var Dice[] $dice
     */
    private array $dice;

    public function setUp(): void
    {
        $dice = [];
        for ($i = 0; $i < 6; ++$i) {
            $die = new Dice();
            $die->roll();
            $dice[$i] = $die;
        }
        $this->dice = $dice;
    }

    /**
     * @param Dice $die
     * @param int $number
     * @return Dice
     */
    private function setDieNumber(Dice $die, int $number): Dice
    {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        $reflection = new \ReflectionObject($die);
        $reflectionNumber = $reflection->getProperty('number');
        $reflectionNumber->setAccessible(true);
        $reflectionNumber->setValue($die, $number);

        return $die;
    }

    /**
     * @test
     */
    public function setDieNumber_SetsDieNumber(): void
    {
        $this->setDieNumber($this->dice[0], 6);
        $die = $this->dice[0];
        self::assertEquals(6, $die->getNumber());
    }

    /**
     * @param array $numbers
     */
    private function setDice(array $numbers): void
    {
        foreach ($numbers as $die => $number) {
            $this->setDieNumber($this->dice[$die], $number);
        }
    }

    /**
     * @test
     */
    public function setDice_setsDice(): void
    {
        $this->setDice([1, 2, 3, 4, 5, 6]);
        self::assertEquals(1, $this->dice[0]->getNumber());
        self::assertEquals(2, $this->dice[1]->getNumber());
        self::assertEquals(3, $this->dice[2]->getNumber());
        self::assertEquals(4, $this->dice[3]->getNumber());
        self::assertEquals(5, $this->dice[4]->getNumber());
        self::assertEquals(6, $this->dice[5]->getNumber());
    }

    /**
     * @test
     */
    public function scoreAces_ScoresAces(): void
    {
        // 0 Aces = 0
        $this->setDice([2, 3, 4, 5, 6]);
        $score = new RollScore($this->dice);
        self::assertEquals(0, $score->scoreAces());

        // 1 Ace = 1
        $this->setDieNumber($this->dice[0], 1);
        $score = new RollScore($this->dice);
        self::assertEquals(1, $score->scoreAces());

        // 2 Aces = 2
        $this->setDieNumber($this->dice[1], 1);
        $score = new RollScore($this->dice);
        self::assertEquals(2, $score->scoreAces());
    }

    /**
     * @test
     */
    public function scoreTwos_ScoresTwos(): void
    {
        // 1 Two = 2
        $this->setDice([1, 2, 1, 4, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(2, $score->scoreTwos());

        // 2 Twos = 4
        $this->setDice([2, 2, 1, 4, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(4, $score->scoreTwos());
    }

    /**
     * @test
     */
    public function scoreThrees_ScoresThrees(): void
    {
        // 2 Threes = 6
        $this->setDice([1, 2, 3, 3, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(6, $score->scoreThrees());

        // 3 Threes = 0
        $this->setDice([3, 2, 3, 3, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(9, $score->scoreThrees());
    }

    /**
     * @test
     */
    public function scoreFours_ScoresFours(): void
    {
        // 0 Fours = 0
        $this->setDice([1, 2, 3, 3, 6]);
        $score = new RollScore($this->dice);
        self::assertEquals(0, $score->scoreFours());

        // 3 Fours = 12
        $this->setDice([4, 2, 4, 3, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(12, $score->scoreFours());
    }

    /**
     * @test
     */
    public function scoreFives_ScoresFives(): void
    {
        // 3 Fives = 15
        $this->setDice([5, 5, 5, 4, 3]);
        $score = new RollScore($this->dice);
        self::assertEquals(15, $score->scoreFives());

        // 5 Fives = 25
        $this->setDice([5, 5, 5, 5, 5]);
        $score = new RollScore($this->dice);
        self::assertEquals(25, $score->scoreFives());
    }

    /**
     * @test
     */
    public function scoreSizes_ScoresSixes(): void
    {
        // 4 Sixes = 24
        $this->setDice([6, 1, 6, 6, 6]);
        $score = new RollScore($this->dice);
        self::assertEquals(24, $score->scoreSixes());

        // 2 Sizes = 12
        $this->setDice([5, 6, 5, 6, 4]);
        $score = new RollScore($this->dice);
        self::assertEquals(12, $score->scoreSixes());
    }

    /**
     * @test
     */
    public function isTrips_withoutThreeOfAKind_isFalse(): RollScore
    {
        $this->setDice([1, 1, 2, 2, 3]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isTrips());

        return $score;
    }

    /**
     * @depends isTrips_withoutThreeOfAKind_isFalse
     * @param RollScore $score
     * @test
     */
    public function ScoreTrips_withoutTrips_returnsZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreTrips());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isTrips_withThreeOfAKind_isTrue(): RollScore
    {
        $this->setDice([1, 1, 1, 1, 1]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isTrips());

        $this->setDice([1, 1, 1, 1, 3]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isTrips());

        $this->setDice([1, 1, 2, 1, 3]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isTrips());

        return $score;
    }

    /**
     * @depends isTrips_withThreeOfAKind_isTrue
     * @param RollScore $score
     * @test
     */
    public function ScoreTrips_withTrips_returnsSumOfDice(RollScore $score): void
    {
        self::assertEquals(8, $score->scoreTrips());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isQuads_withoutFourOfAKind_isFalse(): RollScore
    {
        $this->setDice([1, 1, 2, 2, 3]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isQuads());

        return $score;
    }

    /**
     * @depends isQuads_withoutFourOfAKind_isFalse
     * @param RollScore $score
     * @test
     */
    public function ScoreQuads_withoutQuads_returnsZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreQuads());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isQuads_withFourOfAKind_isTrue(): RollScore
    {
        $this->setDice([1, 1, 1, 1, 2]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isQuads());

        $this->setDice([1, 1, 1, 1, 3]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isQuads());

        return $score;
    }

    /**
     * @depends isQuads_withFourOfAKind_isTrue
     * @param RollScore $score
     * @test
     */
    public function ScoreQuads_withQuads_returnsSumOfDice(RollScore $score): void
    {
        self::assertEquals(7, $score->scoreQuads());
    }

    /**
     * @test
     */
    public function isYachtSee_withoutFiveOfAKind_isFalse(): RollScore
    {
        $this->setDice([1, 1, 2, 2, 3]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isYachtSee());

        return $score;
    }

    /**
     * @depends isYachtSee_withoutFiveOfAKind_isFalse
     * @param RollScore $score
     * @test
     */
    public function ScoreYachtSee_withoutYachtSee_returnsZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreYachtSee());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isYachtSee_withYachtSee_isTrue(): RollScore
    {
        $this->setDice([1, 1, 1, 1, 1]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isYachtSee());

        return $score;
    }

    /**
     * @depends isYachtSee_withYachtSee_isTrue
     * @param RollScore $score
     * @test
     */
    public function ScoreYachtSee_withYachtSee_returnsFifty(RollScore $score): void
    {
        self::assertEquals(RollScore::SCORE_YACHTSEE, $score->scoreYachtSee());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isFullHouse_withFullHouse_isTrue(): RollScore
    {
        $this->setDice([1, 2, 1, 2, 1]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isFullHouse());

        $this->setDice([4, 4, 4, 4, 4]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isFullHouse());

        return $score;
    }

    /**
     * @depends isFullHouse_withFullHouse_isTrue
     * @param RollScore $score
     * @test
     */
    public function scoreFullHouse_withFullHouse_isFullHouse(RollScore $score): void
    {
        self::assertEquals(RollScore::SCORE_FULL_HOUSE, $score->scoreFullHouse());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isFullHouse_withoutFullHouse_isFalse(): RollScore
    {
        $this->setDice([1, 2, 3, 1, 2]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isFullHouse());

        return $score;
    }

    /**
     * @depends isFullHouse_withoutFullHouse_isFalse
     * @param RollScore $score
     * @test
     */
    public function scoreFullHouse_withoutFullHouse_scoresZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreFullHouse());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isSmallStraight_withSmallStraight_isTrue(): RollScore
    {
        $this->setDice([1, 2, 3, 4, 1]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isSmallStraight());

        $this->setDice([1, 2, 3, 4, 5]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isSmallStraight());

        $this->setDice([4, 2, 3, 4, 5]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isSmallStraight());

        return $score;
    }

    /**
     * @depends isSmallStraight_withSmallStraight_isTrue
     * @param RollScore $score
     * @test
     */
    public function scoreSmallStraight_withSmallStraight_returnsSmallStraight(RollScore $score): void
    {
        self::assertEquals(RollScore::SCORE_SMALL_STRAIGHT, $score->scoreSmallStraight());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isSmallStraight_withoutSmallStraight_isFalse(): RollScore
    {
        $this->setDice([1, 2, 2, 4, 1]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isSmallStraight());

        return $score;
    }

    /**
     * @depends isSmallStraight_withoutSmallStraight_isFalse
     * @param RollScore $score
     * @test
     */
    public function scoreSmallStraight_withoutSmallStraight_isZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreSmallStraight());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isLargeStraight_withLargeStraight_isTrue(): RollScore
    {
        $this->setDice([1, 2, 3, 4, 5]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isLargeStraight());

        $this->setDice([6, 2, 3, 4, 5]);
        $score = new RollScore($this->dice);
        self::assertTrue($score->isLargeStraight());

        return $score;
    }

    /**
     * @depends isLargeStraight_withLargeStraight_isTrue
     * @param RollScore $score
     * @test
     */
    public function scoreLargeStraight_withLargeStraight_returnsLargeStraight(RollScore $score): void
    {
        self::assertEquals(RollScore::SCORE_LARGE_STRAIGHT, $score->scoreLargeStraight());
    }

    /**
     * @return RollScore
     * @test
     */
    public function isLargeStraight_withoutLargeStraight_isFalse(): RollScore
    {
        $this->setDice([1, 2, 2, 4, 1]);
        $score = new RollScore($this->dice);
        self::assertFalse($score->isLargeStraight());

        return $score;
    }

    /**
     * @depends isLargeStraight_withoutLargeStraight_isFalse
     * @param RollScore $score
     * @test
     */
    public function scoreLargeStraight_withoutLargeStraight_isZero(RollScore $score): void
    {
        self::assertEquals(0, $score->scoreLargeStraight());
    }
}
