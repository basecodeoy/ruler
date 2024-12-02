<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler\Operator;

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Proposition;
use BaseCodeOy\Ruler\VariableOperand;

/**
 * A String Contains comparison operator.
 */
final class StringContains extends VariableOperator implements Proposition
{
    /**
     * @param Context $context Context with which to evaluate this Proposition
     */
    #[\Override()]
    public function evaluate(Context $context): bool
    {
        /** @var VariableOperand $left */
        /** @var VariableOperand $right */
        [$left, $right] = $this->getOperands();

        return $left->prepareValue($context)->stringContains($right->prepareValue($context));
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::BINARY;
    }
}