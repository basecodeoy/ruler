<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Proposition;

final class FalseProposition implements Proposition
{
    #[\Override()]
    public function evaluate(Context $context): bool
    {
        return false;
    }
}
