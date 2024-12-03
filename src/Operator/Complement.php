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
 * A Complement Set Operator.
 */
final class Complement extends VariableOperator implements VariableOperand
{
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        $complement = null;

        /** @var VariableOperand $operand */
        foreach ($this->getOperands() as $operand) {
            if (!$complement instanceof Set) {
                $complement = $operand->prepareValue($context)->getSet();
            } else {
                $set = $operand->prepareValue($context)->getSet();
                $complement = $complement->complement($set);
            }
        }

        return $complement;
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::MULTIPLE;
    }
}
