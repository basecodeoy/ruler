<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler\Operator;

use BaseCodeOy\Ruler\Operator as BaseOperator;
use BaseCodeOy\Ruler\VariableOperand;

abstract class VariableOperator extends BaseOperator
{
    /**
     * @param VariableOperand $operand
     */
    #[\Override()]
    public function addOperand($operand): void
    {
        $this->addVariable($operand);
    }

    public function addVariable(VariableOperand $operand): void
    {
        if (static::UNARY === $this->getOperandCardinality()
            && 0 < \count($this->operands)
        ) {
            throw new \LogicException(static::class.' can only have 1 operand');
        }

        $this->operands[] = $operand;
    }
}
