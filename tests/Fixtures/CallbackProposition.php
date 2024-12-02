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

final class CallbackProposition implements Proposition
{
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        if (!\is_callable($callback)) {
            throw new \InvalidArgumentException('CallbackProposition expects a callable argument');
        }

        $this->callback = $callback;
    }

    #[\Override()]
    public function evaluate(Context $context): bool
    {
        return ($this->callback)($context);
    }
}
