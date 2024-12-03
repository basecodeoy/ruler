<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

final class Invokable
{
    public function __invoke(mixed $value = null): Fact
    {
        return new Fact($value);
    }
}
