<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler\Operator;

use BaseCodeOy\Ruler\Proposition;

/**
 * Logical operator base class.
 */
abstract class LogicalOperator extends PropositionOperator implements Proposition
{
    /**
     * array of propositions.
     *
     * @param array<Proposition> $props
     */
    public function __construct(array $props = [])
    {
        foreach ($props as $operand) {
            $this->addOperand($operand);
        }
    }
}
