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
 * A Negation Math Operator.
 */
final class Negation extends VariableOperator implements VariableOperand
{
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        /** @var VariableOperand $operand */
        [$operand] = $this->getOperands();

        return new Value($operand->prepareValue($context)->negate());
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::UNARY;
    }
}
