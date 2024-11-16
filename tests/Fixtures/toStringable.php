<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

final readonly class toStringable implements \Stringable
{
    public function __construct(
        private mixed $thingy = null,
    ) {}

    #[\Override()]
    public function __toString(): string
    {
        return (string) $this->thingy;
    }
}
