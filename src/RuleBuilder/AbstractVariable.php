<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Ruler\RuleBuilder;

use BaseCodeOy\Ruler\AbstractVariable as BaseVariable;
use BaseCodeOy\Ruler\Operator;
use BaseCodeOy\Ruler\Operator\VariableOperator;
use BaseCodeOy\Ruler\RuleBuilder;
use BaseCodeOy\Ruler\VariableOperand;

/**
 * A propositional Variable.
 *
 * Variables are placeholders in Propositions and Comparison Operators. During
 * evaluation, they are replaced with terminal Values, either from the Variable
 * default or from the current Context.
 *
 * The RuleBuilder Variable extends the base Variable class with a fluent
 * interface for creating VariableProperties, Operators and Rules without all
 * kinds of awkward object instantiation.
 */
abstract class AbstractVariable extends BaseVariable implements \ArrayAccess
{
    private array $properties = [];

    /**
     * RuleBuilder Variable constructor.
     *
     * @param string $name  Variable name (default: null)
     * @param mixed  $value Default Variable value (default: null)
     */
    public function __construct(
        private readonly RuleBuilder $ruleBuilder,
        ?string $name = null,
        mixed $value = null,
    ) {
        parent::__construct($name, $value);
    }

    /**
     * Magic method to apply operators registered with RuleBuilder.
     *
     * @see RuleBuilder::registerOperatorNamespace
     *
     * @throws \LogicException if operator is not registered
     *
     * @return Operator|self
     */
    public function __call(string $name, array $args)
    {
        $reflection = new \ReflectionClass($this->ruleBuilder->findOperator($name));
        $args = \array_map([$this, 'asVariable'], $args);
        \array_unshift($args, $this);

        $op = $reflection->newInstanceArgs($args);

        if ($op instanceof VariableOperand) {
            return $this->wrap($op);
        }

        return $op;
    }

    /**
     * Get the RuleBuilder instance set on this Variable.
     */
    public function getRuleBuilder(): RuleBuilder
    {
        return $this->ruleBuilder;
    }

    /**
     * Get a VariableProperty for accessing methods, indexes and properties of
     * the current variable.
     *
     * @param string $name  Property name
     * @param mixed  $value The default VariableProperty value
     */
    public function getProperty(string $name, mixed $value = null): VariableProperty
    {
        if (!\array_key_exists($name, $this->properties)) {
            $this->properties[$name] = new VariableProperty($this, $name, $value);
        }

        return $this->properties[$name];
    }

    /**
     * Fluent interface method for checking whether a VariableProperty has been defined.
     *
     * @param string $name Property name
     */
    #[\Override()]
    public function offsetExists($name): bool
    {
        return \array_key_exists($name, $this->properties);
    }

    /**
     * Fluent interface method for creating or accessing VariableProperties.
     *
     * @see getProperty
     *
     * @param string $name Property name
     */
    #[\Override()]
    public function offsetGet($name): VariableProperty
    {
        return $this->getProperty($name);
    }

    /**
     * Fluent interface method for setting default a VariableProperty value.
     *
     * @see setValue
     *
     * @param string $name  Property name
     * @param mixed  $value The default Variable value
     */
    #[\Override()]
    public function offsetSet($name, mixed $value): void
    {
        $this->getProperty($name)->setValue($value);
    }

    /**
     * Fluent interface method for removing a VariableProperty reference.
     *
     * @param string $name Property name
     */
    #[\Override()]
    public function offsetUnset($name): void
    {
        unset($this->properties[$name]);
    }

    /**
     * Fluent interface helper to create a contains comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function stringContains(mixed $variable): Operator\StringContains
    {
        return new Operator\StringContains($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a contains comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function stringDoesNotContain(mixed $variable): Operator\StringDoesNotContain
    {
        return new Operator\StringDoesNotContain($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a insensitive contains comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function stringContainsInsensitive(mixed $variable): Operator\StringContainsInsensitive
    {
        return new Operator\StringContainsInsensitive($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a GreaterThan comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function greaterThan(mixed $variable): Operator\GreaterThan
    {
        return new Operator\GreaterThan($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a GreaterThanOrEqualTo comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function greaterThanOrEqualTo(mixed $variable): Operator\GreaterThanOrEqualTo
    {
        return new Operator\GreaterThanOrEqualTo($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a LessThan comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function lessThan(mixed $variable): Operator\LessThan
    {
        return new Operator\LessThan($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a LessThanOrEqualTo comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function lessThanOrEqualTo(mixed $variable): Operator\LessThanOrEqualTo
    {
        return new Operator\LessThanOrEqualTo($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a EqualTo comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function equalTo(mixed $variable): Operator\EqualTo
    {
        return new Operator\EqualTo($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a NotEqualTo comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function notEqualTo(mixed $variable): Operator\NotEqualTo
    {
        return new Operator\NotEqualTo($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a SameAs comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function sameAs(mixed $variable): Operator\SameAs
    {
        return new Operator\SameAs($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a NotSameAs comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function notSameAs(mixed $variable): Operator\NotSameAs
    {
        return new Operator\NotSameAs($this, $this->asVariable($variable));
    }

    public function union(...$variables): self
    {
        return $this->applySetOperator('Union', $variables);
    }

    public function intersect(...$variables): self
    {
        return $this->applySetOperator('Intersect', $variables);
    }

    public function complement(...$variables): self
    {
        return $this->applySetOperator('Complement', $variables);
    }

    public function symmetricDifference(...$variables): self
    {
        return $this->applySetOperator('SymmetricDifference', $variables);
    }

    public function min(): self
    {
        return $this->wrap(new Operator\Min($this));
    }

    public function max(): self
    {
        return $this->wrap(new Operator\Max($this));
    }

    public function containsSubset($variable): Operator\ContainsSubset
    {
        return new Operator\ContainsSubset($this, $this->asVariable($variable));
    }

    public function doesNotContainSubset($variable): Operator\DoesNotContainSubset
    {
        return new Operator\DoesNotContainSubset($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a contains comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function setContains(mixed $variable): Operator\SetContains
    {
        return new Operator\SetContains($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a contains comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function setDoesNotContain(mixed $variable): Operator\SetDoesNotContain
    {
        return new Operator\SetDoesNotContain($this, $this->asVariable($variable));
    }

    public function add($variable): self
    {
        return $this->wrap(new Operator\Addition($this, $this->asVariable($variable)));
    }

    public function divide($variable): self
    {
        return $this->wrap(new Operator\Division($this, $this->asVariable($variable)));
    }

    public function modulo($variable): self
    {
        return $this->wrap(new Operator\Modulo($this, $this->asVariable($variable)));
    }

    public function multiply($variable): self
    {
        return $this->wrap(new Operator\Multiplication($this, $this->asVariable($variable)));
    }

    public function subtract($variable): self
    {
        return $this->wrap(new Operator\Subtraction($this, $this->asVariable($variable)));
    }

    public function negate(): self
    {
        return $this->wrap(new Operator\Negation($this));
    }

    public function ceil(): self
    {
        return $this->wrap(new Operator\Ceil($this));
    }

    public function floor(): self
    {
        return $this->wrap(new Operator\Floor($this));
    }

    public function exponentiate($variable): self
    {
        return $this->wrap(new Operator\Exponentiate($this, $this->asVariable($variable)));
    }

    /**
     * Fluent interface helper to create a endsWith comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function endsWith(mixed $variable): Operator\EndsWith
    {
        return new Operator\EndsWith($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a endsWith insensitive comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function endsWithInsensitive(mixed $variable): Operator\EndsWithInsensitive
    {
        return new Operator\EndsWithInsensitive($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a startsWith comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function startsWith(mixed $variable): Operator\StartsWith
    {
        return new Operator\StartsWith($this, $this->asVariable($variable));
    }

    /**
     * Fluent interface helper to create a startsWith insensitive comparison operator.
     *
     * @param mixed $variable Right side of comparison operator
     */
    public function startsWithInsensitive(mixed $variable): Operator\StartsWithInsensitive
    {
        return new Operator\StartsWithInsensitive($this, $this->asVariable($variable));
    }

    /**
     * Private helper to retrieve a Variable instance for the given $variable.
     *
     * @param mixed $variable BaseVariable instance or value
     */
    private function asVariable(mixed $variable): BaseVariable
    {
        return ($variable instanceof BaseVariable) ? $variable : new \BaseCodeOy\Ruler\Variable(null, $variable);
    }

    /**
     * Private helper to apply a set operator.
     */
    private function applySetOperator(string $name, array $args): self
    {
        $reflection = new \ReflectionClass('\\BaseCodeOy\Ruler\\Operator\\'.$name);
        \array_unshift($args, $this);

        return $this->wrap($reflection->newInstanceArgs($args));
    }

    /**
     * Private helper to wrap a VariableOperator in a Variable instance.
     */
    private function wrap(VariableOperator $op): self
    {
        return new static($this->ruleBuilder, null, $op);
    }
}
