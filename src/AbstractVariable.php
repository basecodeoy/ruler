<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * A propositional Variable.
 *
 * Variables are placeholders in Propositions and Comparison Operators. During
 * evaluation, they are replaced with terminal Values, either from the Variable
 * default or from the current Context.
 */
abstract class AbstractVariable implements VariableOperand
{
    /**
     * Variable class constructor.
     *
     * @param string $name  Variable name (default: null)
     * @param mixed  $value Default Variable value (default: null)
     */
    public function __construct(
        private readonly ?string $name = null,
        private mixed $value = null,
    ) {}

    /**
     * Return the Variable name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the default Variable value.
     *
     * @param mixed $value The default Variable value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Get the default Variable value.
     *
     * @return mixed Variable value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Prepare a Value for this Variable given the current Context.
     *
     * @param Context $context The current Context
     */
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        if (isset($this->name, $context[$this->name])) {
            $value = $context[$this->name];
        } elseif ($this->value instanceof VariableOperand) {
            $value = $this->value->prepareValue($context);
        } else {
            $value = $this->value;
        }

        return ($value instanceof AbstractValue) ? $value : new Value($value);
    }
}
