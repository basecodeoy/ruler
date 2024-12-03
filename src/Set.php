<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler;

/**
 * A Ruler Set.
 *
 * A Set is essentially an array, a special case of Value which can be compared
 * by SetOperators.
 */
final class Set extends AbstractValue implements \Countable, \Stringable
{
    /**
     * Set constructor.
     *
     * A Set object is immutable, and is used by Variables for comparing their
     * Default values or facts from the current Context.
     *
     * @param mixed $set Immutable value represented by this Value object
     */
    public function __construct(mixed $set)
    {
        parent::__construct($set);

        if (!\is_array($this->value)) {
            $this->value = null === $this->value ? [] : [$this->value];
        }

        foreach ($this->value as &$value) {
            if (\is_array($value)) {
                $value = new self($value);
            } elseif (\is_object($value)) {
                if (!\method_exists($value, '__toString')) {
                    $value = new Value($value);
                }
            }
        }

        $this->value = \array_unique($this->value);

        foreach ($this->value as &$value) {
            if ($value instanceof AbstractValue && !$value instanceof self) {
                $value = $value->getValue();
            }
        }
    }

    #[\Override()]
    public function __toString(): string
    {
        $returnValue = '';

        foreach ($this->value as $value) {
            $returnValue .= (string) $value;
        }

        return $returnValue;
    }

    /**
     * Set Contains comparison.
     *
     * @param AbstractValue $value Value object to compare against
     */
    public function setContains(AbstractValue $value): bool
    {
        if (\is_array($value->getValue())) {
            foreach ($this->value as $val) {
                if ($val instanceof self && is_loosely_equal($val, $value->getSet())) {
                    return true;
                }
            }

            return false;
        }

        return \in_array($value->getValue(), $this->value, true);
    }

    /**
     * Set union operator.
     *
     * Returns a Set which is the union of this Set with all passed Sets.
     *
     * @param AbstractValue ...$value One or more Sets
     */
    public function union(AbstractValue ...$value): self
    {
        $union = $this->value;

        /** @var Value $arg */
        foreach ($value as $arg) {
            /** @var array $convertedArg */
            $convertedArg = $arg->getSet()->getValue();
            $union = \array_merge($union, \array_diff($convertedArg, $union));
        }

        return new self($union);
    }

    /**
     * Set intersection operator.
     *
     * Returns a Set which is the intersection of this Set with all passed sets.
     *
     * @param AbstractValue ...$value One or more Sets
     */
    public function intersect(AbstractValue ...$value): self
    {
        $intersect = $this->value;

        /** @var Value $arg */
        foreach ($value as $arg) {
            /** @var array $convertedArg */
            $convertedArg = $arg->getSet()->getValue();
            // array_values is needed to make sure the indexes are ordered from 0
            $intersect = \array_values(\array_intersect($intersect, $convertedArg));
        }

        return new self($intersect);
    }

    /**
     * Set complement operator.
     *
     * Returns a Set which is the complement of this Set with all passed Sets.
     *
     * @param AbstractValue ...$value One or more Sets
     */
    public function complement(AbstractValue ...$value): self
    {
        $complement = $this->value;

        /** @var Value $arg */
        foreach ($value as $arg) {
            /** @var array $convertedArg */
            $convertedArg = $arg->getSet()->getValue();
            // array_values is needed to make sure the indexes are ordered from 0
            $complement = \array_values(\array_diff($complement, $convertedArg));
        }

        return new self($complement);
    }

    /**
     * Set symmetric difference operator.
     *
     * Returns a Set which is the symmetric difference of this Set with the
     * passed Set.
     */
    public function symmetricDifference(AbstractValue $value): self
    {
        $returnValue = new self([]);

        return $returnValue->union(
            $this->complement($value),
            $value->getSet()->complement($this),
        );
    }

    /**
     * Numeric minimum value in this Set.
     *
     * @throws \RuntimeException if this Set contains non-numeric members
     *
     * @return mixed
     */
    public function min()
    {
        if (!$this->isValidNumericSet()) {
            throw new \RuntimeException('min: all values must be numeric');
        }

        if (empty($this->value)) {
            return null;
        }

        return \min($this->value);
    }

    /**
     * Numeric maximum value in this Set.
     *
     * @throws \RuntimeException if this Set contains non-numeric members
     *
     * @return mixed
     */
    public function max()
    {
        if (!$this->isValidNumericSet()) {
            throw new \RuntimeException('max: all values must be numeric');
        }

        if (empty($this->value)) {
            return null;
        }

        return \max($this->value);
    }

    /**
     * Contains Subset comparison.
     */
    public function containsSubset(self $set): bool
    {
        if ((\is_countable($set->getValue()) ? \count($set->getValue()) : 0) > (\is_countable($this->getValue()) ? \count($this->getValue()) : 0)) {
            return false;
        }

        return \array_intersect($set->getValue(), $this->getValue()) === $set->getValue();
    }

    #[\Override()]
    public function count(): int
    {
        return \is_countable($this->value) ? \count($this->value) : 0;
    }

    /**
     * Helper function to validate that a set contains only numeric members.
     */
    private function isValidNumericSet(): bool
    {
        return (\is_countable($this->value) ? \count($this->value) : 0) === \array_sum(\array_map('is_numeric', $this->value));
    }
}
