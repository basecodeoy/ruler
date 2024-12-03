<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\RuleBuilder;
use BaseCodeOy\Ruler\RuleBuilder\Variable;
use BaseCodeOy\Ruler\RuleBuilder\VariableProperty;

test('constructor', function (): void {
    $name = 'evil';
    $var = new Variable(new RuleBuilder(), $name);
    self::assertEquals($name, $var->getName());
    self::assertNull($var->getValue());
});

test('get set value', function (): void {
    $values = \explode(', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it');

    $variable = new Variable(new RuleBuilder(), 'technologic');

    foreach ($values as $value) {
        $variable->setValue($value);
        self::assertEquals($value, $variable->getValue());
    }
});

test('prepare value', function (): void {
    $values = [
        'one' => 'Foo',
        'two' => 'BAR',
        'three' => fn (): string => 'baz',
    ];

    $context = new Context($values);

    $rb = new RuleBuilder();
    $varA = new Variable($rb, 'four', 'qux');
    self::assertInstanceOf(BaseCodeOy\Ruler\Value::class, $varA->prepareValue($context));
    self::assertEquals(
        'qux',
        $varA->prepareValue($context)->getValue(),
        "Variables should return the default value if it's missing from the context.",
    );

    $varB = new Variable($rb, 'one', 'FAIL');
    self::assertEquals(
        'Foo',
        $varB->prepareValue($context)->getValue(),
    );

    $varC = new Variable($rb, 'three', 'FAIL');
    self::assertEquals(
        'baz',
        $varC->prepareValue($context)->getValue(),
    );

    $varD = new Variable($rb, null, 'qux');
    self::assertInstanceOf(BaseCodeOy\Ruler\Value::class, $varD->prepareValue($context));
    self::assertEquals(
        'qux',
        $varD->prepareValue($context)->getValue(),
        "Anonymous variables don't require a name to prepare value",
    );
});

test('fluent interface helpers and anonymous variables', function (): void {
    $rb = new RuleBuilder();
    $context = new Context([
        'a' => 1,
        'b' => 2,
        'c' => [1, 4],
        'd' => [
            'foo' => 1,
            'bar' => 2,
            'baz' => [
                'qux' => 3,
            ],
        ],
        'e' => 1.5,
    ]);

    $varA = new Variable($rb, 'a');
    $varB = new Variable($rb, 'b');
    $varC = new Variable($rb, 'c');
    $varD = new Variable($rb, 'd');
    $varE = new Variable($rb, 'e');

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\GreaterThan::class, $varA->greaterThan(0));
    self::assertTrue($varA->greaterThan(0)->evaluate($context));
    self::assertFalse($varA->greaterThan(2)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\GreaterThanOrEqualTo::class, $varA->greaterThanOrEqualTo(0));
    self::assertTrue($varA->greaterThanOrEqualTo(0)->evaluate($context));
    self::assertTrue($varA->greaterThanOrEqualTo(1)->evaluate($context));
    self::assertFalse($varA->greaterThanOrEqualTo(2)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LessThan::class, $varA->lessThan(0));
    self::assertTrue($varA->lessThan(2)->evaluate($context));
    self::assertFalse($varA->lessThan(0)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LessThanOrEqualTo::class, $varA->lessThanOrEqualTo(0));
    self::assertTrue($varA->lessThanOrEqualTo(1)->evaluate($context));
    self::assertTrue($varA->lessThanOrEqualTo(2)->evaluate($context));
    self::assertFalse($varA->lessThanOrEqualTo(0)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\EqualTo::class, $varA->equalTo(0));
    self::assertTrue($varA->equalTo(1)->evaluate($context));
    self::assertFalse($varA->equalTo(0)->evaluate($context));
    self::assertFalse($varA->equalTo(2)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\NotEqualTo::class, $varA->notEqualTo(0));
    self::assertFalse($varA->notEqualTo(1)->evaluate($context));
    self::assertTrue($varA->notEqualTo(0)->evaluate($context));
    self::assertTrue($varA->notEqualTo(2)->evaluate($context));

    $this->assertInstanceof(Variable::class, $varA->add(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Addition::class, $varA->add(3)->getValue());
    $this->assertInstanceof(BaseCodeOy\Ruler\Value::class, $varA->add(3)->prepareValue($context));
    self::assertEquals(4, $varA->add(3)->prepareValue($context)->getValue());
    self::assertEquals(0, $varA->add(-1)->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varE->ceil());
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Ceil::class, $varE->ceil()->getValue());
    self::assertEquals(2, $varE->ceil()->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varB->divide(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Division::class, $varB->divide(3)->getValue());
    self::assertEquals(1, $varB->divide(2)->prepareValue($context)->getValue());
    self::assertEquals(-2, $varB->divide(-1)->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varE->floor());
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Floor::class, $varE->floor()->getValue());
    self::assertEquals(1, $varE->floor()->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varA->modulo(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Modulo::class, $varA->modulo(3)->getValue());
    self::assertEquals(1, $varA->modulo(3)->prepareValue($context)->getValue());
    self::assertEquals(0, $varB->modulo(2)->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varA->multiply(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Multiplication::class, $varA->multiply(3)->getValue());
    self::assertEquals(6, $varB->multiply(3)->prepareValue($context)->getValue());
    self::assertEquals(-2, $varB->multiply(-1)->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varA->negate());
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Negation::class, $varA->negate()->getValue());
    self::assertEquals(-1, $varA->negate()->prepareValue($context)->getValue());
    self::assertEquals(-2, $varB->negate()->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varA->subtract(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Subtraction::class, $varA->subtract(3)->getValue());
    self::assertEquals(-2, $varA->subtract(3)->prepareValue($context)->getValue());
    self::assertEquals(2, $varA->subtract(-1)->prepareValue($context)->getValue());

    $this->assertInstanceof(Variable::class, $varA->exponentiate(3));
    $this->assertInstanceof(BaseCodeOy\Ruler\Operator\Exponentiate::class, $varA->exponentiate(3)->getValue());
    self::assertEquals(1, $varA->exponentiate(3)->prepareValue($context)->getValue());
    self::assertEquals(1, $varA->exponentiate(-1)->prepareValue($context)->getValue());
    self::assertEquals(8, $varB->exponentiate(3)->prepareValue($context)->getValue());
    self::assertEquals(0.5, $varB->exponentiate(-1)->prepareValue($context)->getValue());

    self::assertFalse($varA->greaterThan($varB)->evaluate($context));
    self::assertTrue($varA->lessThan($varB)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\SetContains::class, $varC->setContains(1));
    self::assertTrue($varC->setContains($varA)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\SetDoesNotContain::class, $varC->setDoesNotContain(1));
    self::assertTrue($varC->setDoesNotContain($varB)->evaluate($context));

    self::assertInstanceOf(VariableProperty::class, $varD['bar']);
    self::assertEquals('foo', $varD['foo']->getName());
    self::assertTrue($varD['foo']->equalTo(1)->evaluate($context));

    self::assertInstanceOf(VariableProperty::class, $varD['foo']);
    self::assertEquals('bar', $varD['bar']->getName());
    self::assertTrue($varD['bar']->equalTo(2)->evaluate($context));

    self::assertInstanceOf(VariableProperty::class, $varD['baz']['qux']);
    self::assertEquals('qux', $varD['baz']['qux']->getName());
    self::assertTrue($varD['baz']['qux']->equalTo(3)->evaluate($context));
});

test('array access', function (): void {
    $var = new Variable(new RuleBuilder());
    self::assertInstanceOf(ArrayAccess::class, $var);

    $foo = $var['foo'];
    $bar = $var['bar'];
    self::assertInstanceOf(VariableProperty::class, $foo);
    self::assertInstanceOf(VariableProperty::class, $bar);

    self::assertSame($var['foo'], $foo);
    self::assertSame($var['bar'], $bar);
    self::assertNotSame($foo, $bar);

    self::assertArrayHasKey('foo', $var);
    self::assertArrayHasKey('bar', $var);

    self::assertArrayNotHasKey('baz', $var);
    self::assertArrayNotHasKey('qux', $var);

    $variableProperty = $var->getProperty('baz');
    self::assertArrayHasKey('baz', $var);

    $qux = $var['qux'];
    self::assertArrayHasKey('qux', $var);

    unset($var['foo'], $var['bar'], $var['baz']);

    self::assertArrayNotHasKey('foo', $var);
    self::assertArrayNotHasKey('bar', $var);
    self::assertArrayNotHasKey('baz', $var);
    self::assertArrayHasKey('qux', $var);
});
