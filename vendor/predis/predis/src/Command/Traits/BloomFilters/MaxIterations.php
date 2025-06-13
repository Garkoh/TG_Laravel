<?php

/*
 * This file is part of the Predis package.
 *
 * (c) 2009-2020 Daniele Alessandri
 * (c) 2021-2025 Till Krüss
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Traits\BloomFilters;

use Predis\Command\Command;
use UnexpectedValueException;

/**
 * @mixin Command
 */
trait MaxIterations
{
    private static $maxIterationsModifier = 'MAXITERATIONS';

    public function setArguments(array $arguments)
    {
        $argumentsLength = count($arguments);

        if (static::$maxIterationsArgumentPositionOffset >= $argumentsLength) {
            parent::setArguments($arguments);

            return;
        }

        if ($arguments[static::$maxIterationsArgumentPositionOffset] === -1) {
            array_splice($arguments, static::$maxIterationsArgumentPositionOffset, 1, [false]);
            parent::setArguments($arguments);

            return;
        }

        if ($arguments[static::$maxIterationsArgumentPositionOffset] < 1) {
            throw new UnexpectedValueException('Wrong max iterations argument value or position offset');
        }

        $argument = $arguments[static::$maxIterationsArgumentPositionOffset];
        $argumentsBefore = array_slice($arguments, 0, static::$maxIterationsArgumentPositionOffset);
        $argumentsAfter = array_slice($arguments, static::$maxIterationsArgumentPositionOffset + 1);

        parent::setArguments(array_merge(
            $argumentsBefore,
            [self::$maxIterationsModifier],
            [$argument],
            $argumentsAfter
        ));
    }
}
