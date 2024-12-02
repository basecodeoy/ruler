<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * A Ruler RuleSet.
 */
final class RuleSet
{
    private array $rules = [];

    /**
     * RuleSet constructor.
     *
     * @param array $rules Rules to add to RuleSet
     */
    public function __construct(array $rules = [])
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Add a Rule to the RuleSet.
     *
     * Adding duplicate Rules to the RuleSet will have no effect.
     *
     * @param Rule $rule Rule to add to the set
     */
    public function addRule(Rule $rule): void
    {
        $this->rules[\spl_object_hash($rule)] = $rule;
    }

    /**
     * Execute all Rules in the RuleSet.
     *
     * @param Context $context Context with which to execute each Rule
     */
    public function executeRules(Context $context): void
    {
        foreach ($this->rules as $rule) {
            $rule->execute($context);
        }
    }
}
