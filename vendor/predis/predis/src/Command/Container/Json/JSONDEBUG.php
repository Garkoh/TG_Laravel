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

namespace Predis\Command\Container\Json;

use Predis\Command\Container\AbstractContainer;

/**
 * @method array memory(string $key, string $path)
 * @method array help()
 */
class JSONDEBUG extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'JSONDEBUG';
    }
}
