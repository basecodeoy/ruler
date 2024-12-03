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
use BaseCodeOy\Ruler\Set;
use BaseCodeOy\Ruler\VariableOperand;

/**
 * A Set Union Operator.
 */
final class Union extends VariableOperator implements VariableOperand
{
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        $union = new Set([]);

        /** @var VariableOperand $operand */
        foreach ($this->getOperands() as $operand) {
            $set = $operand->prepareValue($context)->getSet();
            $union = $union->union($set);
        }

        return $union;
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::MULTIPLE;
    }
}
