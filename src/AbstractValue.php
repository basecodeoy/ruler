<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * A Ruler Value.
 *
 * A Value represents a comparable terminal value. Variables and Comparison Operators
 * are resolved to Values by applying the current Context and the default Variable value.
 */
abstract class AbstractValue implements \Stringable
{
    /**
     * Value constructor.
     *
     * A Value object is immutable, and is used by Variables for comparing their default
     * values or facts from the current Context.
     *
     * @param mixed $value Immutable value represented by this Value object
     */
    public function __construct(
        protected mixed $value,
    ) {}

    #[\Override()]
    public function __toString(): string
    {
        if (\is_object($this->value)) {
            return \spl_object_hash($this->value);
        }

        return \serialize($this->value);
    }

    /**
     * Return the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get a Set containing this Value.
     */
    public function getSet(): Set
    {
        return new Set($this->value);
    }

    /**
     * Equal To comparison.
     *
     * @param self $value Value object to compare against
     */
    public function equalTo(self $value): bool
    {
        return $this->value === $value->getValue();
    }

    /**
     * Identical To comparison.
     *
     * @param self $value Value object to compare against
     */
    public function sameAs(self $value): bool
    {
        return $this->value === $value->getValue();
    }

    /**
     * String Contains comparison.
     *
     * @param self $value Value object to compare against
     */
    public function stringContains(self $value): bool
    {
        return \str_contains((string) $this->value, (string) $value->getValue());
    }

    /**
     * String Contains case-insensitive comparison.
     *
     * @param self $value Value object to compare against
     */
    public function stringContainsInsensitive(self $value): bool
    {
        return \mb_stripos((string) $this->value, (string) $value->getValue()) !== false;
    }

    /**
     * Greater Than comparison.
     *
     * @param self $value Value object to compare against
     */
    public function greaterThan(self $value): bool
    {
        return $this->value > $value->getValue();
    }

    /**
     * Less Than comparison.
     *
     * @param self $value Value object to compare against
     */
    public function lessThan(self $value): bool
    {
        return $this->value < $value->getValue();
    }

    public function add(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return $this->value + $value->getValue();
    }

    public function divide(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        if (0 === $value->getValue()) {
            throw new \RuntimeException('Division by zero');
        }

        return $this->value / $value->getValue();
    }

    public function modulo(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        if (0 === $value->getValue()) {
            throw new \RuntimeException('Division by zero');
        }

        return $this->value % $value->getValue();
    }

    public function multiply(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return $this->value * $value->getValue();
    }

    public function subtract(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return $this->value - $value->getValue();
    }

    public function negate()
    {
        if (!\is_numeric($this->value)) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return -$this->value;
    }

    public function ceil()
    {
        if (!\is_numeric($this->value)) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return (int) \ceil($this->value);
    }

    public function floor()
    {
        if (!\is_numeric($this->value)) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return (int) \floor($this->value);
    }

    public function exponentiate(self $value)
    {
        if (!\is_numeric($this->value) || !\is_numeric($value->getValue())) {
            throw new \RuntimeException('Arithmetic: values must be numeric');
        }

        return $this->value ** $value->getValue();
    }

    /**
     * String StartsWith comparison.
     *
     * @param self $value       Value object to compare against
     * @param bool $insensitive Perform a case-insensitive comparison (default: false)
     */
    public function startsWith(self $value, bool $insensitive = false): bool
    {
        $value = $value->getValue();
        $valueLength = \mb_strlen((string) $value);

        if (!empty($this->value) && !empty($value) && \mb_strlen((string) $this->value) >= $valueLength) {
            return \substr_compare((string) $this->value, (string) $value, 0, $valueLength, $insensitive) === 0;
        }

        return false;
    }

    /**
     * String EndsWith comparison.
     *
     * @param self $value       Value object to compare against
     * @param bool $insensitive Perform a case-insensitive comparison (default: false)
     */
    public function endsWith(self $value, bool $insensitive = false): bool
    {
        $value = $value->getValue();
        $valueLength = \mb_strlen((string) $value);

        if (!empty($this->value) && !empty($value) && \mb_strlen((string) $this->value) >= $valueLength) {
            return \substr_compare((string) $this->value, (string) $value, -$valueLength, $valueLength, $insensitive) === 0;
        }

        return false;
    }
}
