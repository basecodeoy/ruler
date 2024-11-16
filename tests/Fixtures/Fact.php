<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

final class Fact
{
    public $value;

    public function __construct(mixed $value = null)
    {
        if ($value !== null) {
            $this->value = $value;
        }
    }
}
