<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * Rule class.
 *
 * A Rule is a conditional Proposition with an (optional) action which is
 * executed upon successful evaluation.
 */
final class Rule implements Proposition
{
    /**
     * Rule constructor.
     *
     * @param Proposition $proposition Propositional condition for this Rule
     * @param callable    $action      Action (callable) to take upon successful Rule execution (default: null)
     */
    public function __construct(
        private readonly Proposition $proposition,
        private $action = null,
    ) {}

    /**
     * Evaluate the Rule with the given Context.
     *
     * @param Context $context Context with which to evaluate this Rule
     */
    #[\Override()]
    public function evaluate(Context $context): bool
    {
        return $this->proposition->evaluate($context);
    }

    /**
     * Execute the Rule with the given Context.
     *
     * The Rule will be evaluated, and if successful, will execute its
     * $action callback.
     *
     * @param Context $context Context with which to execute this Rule
     *
     * @throws \LogicException
     */
    public function execute(Context $context): void
    {
        if ($this->evaluate($context) && $this->action !== null) {
            if (!\is_callable($this->action)) {
                throw new \LogicException('Rule actions must be callable.');
            }

            ($this->action)();
        }
    }
}
