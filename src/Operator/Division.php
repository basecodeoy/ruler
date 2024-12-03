<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler\Operator;

use BaseCodeOy\Ruler\AbstractValue;
use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Value;
use BaseCodeOy\Ruler\VariableOperand;

/**
 * A Division Arithmetic Operator.
 */
final class Division extends VariableOperator implements VariableOperand
{
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        /** @var VariableOperand $left */
        /** @var VariableOperand $right */
        [$left, $right] = $this->getOperands();

        return new Value($left->prepareValue($context)->divide($right->prepareValue($context)));
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::BINARY;
    }
}
