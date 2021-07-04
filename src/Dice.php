<?php

declare(strict_types=1);

namespace Jefhar\Yachtsee;

use Jefhar\Yachtsee\Exceptions\UnrolledDieException;

/**
 * Class Dice
 */
class Dice
{
    private int $number = -1;
    private bool $frozen = false;

    /**
     *
     */
    public function roll(): void
    {
        if (!$this->frozen) {
            /** @noinspection RandomApiMigrationInspection */
            $this->number = rand(1, 6);
        }
    }

    /**
     * @return int
     * @throws UnrolledDieException
     */
    public function getNumber(): int
    {
        if ($this->number === -1) {
            throw new UnrolledDieException();
        }

        return $this->number;
    }

    /**
     * Freeze the die so it cannot be rolled.
     */
    public function freeze(): void
    {
        $this->frozen = true;
    }

    /**
     * Unfreeze the die so it may be rolled.
     */
    public function unfreeze(): void
    {
        $this->frozen = false;
    }
}
