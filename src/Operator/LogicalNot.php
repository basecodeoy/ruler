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

/**
 * A logical NOT operator.
 */
final class LogicalNot extends LogicalOperator
{
    /**
     * @param Context $context Context with which to evaluate this Proposition
     */
    #[\Override()]
    public function evaluate(Context $context): bool
    {
        /** @var Proposition $operand */
        [$operand] = $this->getOperands();

        return !$operand->evaluate($context);
    }

    #[\Override()]
    protected function getOperandCardinality(): string
    {
        return self::UNARY;
    }
}
