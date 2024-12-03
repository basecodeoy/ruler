<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * A propositional VariableProperty.
 *
 * A VariableProperty is a special propositional Variable which maps to a
 * property, method or offset of another Variable. During evaluation, they are
 * replaced with terminal Values from properties of their parent Variable,
 * either from their default Value, or from the current Context.
 */
final class VariableProperty extends AbstractVariable
{
    /**
     * VariableProperty class constructor.
     *
     * @param Variable $variable Parent Variable instance
     * @param string   $name     Property name
     * @param mixed    $value    Default Property value (default: null)
     */
    public function __construct(
        private readonly Variable $variable,
        ?string $name,
        mixed $value = null,
    ) {
        parent::__construct($name, $value);
    }

    /**
     * Prepare a Value for this VariableProperty given the current Context.
     *
     * To retrieve a Value, the parent Variable is first resolved given the
     * current context. Then, depending on its type, a method, property or
     * offset of the parent Value is returned.
     *
     * If the parent Value is an object, and this VariableProperty name is
     * "bar", it will do a prioritized lookup for:
     *
     *  1. A method named `bar`
     *  2. A public property named `bar`
     *  3. ArrayAccess + offsetExists named `bar`
     *
     * If it is an array:
     *
     *  1. Array index `bar`
     *
     * Otherwise, return the default value for this VariableProperty.
     *
     * @param Context $context The current Context
     */
    #[\Override()]
    public function prepareValue(Context $context): AbstractValue
    {
        $name = $this->getName();
        $value = $this->variable->prepareValue($context)->getValue();

        if (\is_object($value) && !$value instanceof \Closure) {
            if (\method_exists($value, $name)) {
                return $this->asValue(\call_user_func([$value, $name]));
            }

            if (isset($value->{$name})) {
                return $this->asValue($value->{$name});
            }

            if ($value instanceof \ArrayAccess && $value->offsetExists($name)) {
                return $this->asValue($value->offsetGet($name));
            }
        } elseif (\is_array($value) && \array_key_exists($name, $value)) {
            return $this->asValue($value[$name]);
        }

        return $this->asValue($this->getValue());
    }

    /**
     * Private helper to retrieve a Value instance for the given $value.
     *
     * @param mixed $value Value instance or value
     */
    private function asValue(mixed $value): AbstractValue
    {
        return ($value instanceof AbstractValue) ? $value : new Value($value);
    }
}
