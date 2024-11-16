<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * The Proposition interface represents a propositional statement.
 */
interface Proposition
{
    /**
     * Evaluate the Proposition with the given Context.
     *
     * @param Context $context Context with which to evaluate this Proposition
     */
    public function evaluate(Context $context): bool;
}
