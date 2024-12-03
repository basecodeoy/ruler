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
    $prop = new VariableProperty(new Variable(new RuleBuilder()), $name);
    self::assertEquals($name, $prop->getName());
    self::assertNull($prop->getValue());
});

test('get set value', function (): void {
    $values = \explode(', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it');

    $prop = new VariableProperty(new Variable(new RuleBuilder()), 'technologic');

    foreach ($values as $value) {
        $prop->setValue($value);
        self::assertEquals($value, $prop->getValue());
    }
});

test('prepare value', function (): void {
    $values = [
        'root' => [
            'one' => 'Foo',
            'two' => 'BAR',
        ],
    ];

    $context = new Context($values);

    $var = new Variable(new RuleBuilder(), 'root');

    $propA = new VariableProperty($var, 'undefined', 'default');
    self::assertInstanceOf(BaseCodeOy\Ruler\Value::class, $propA->prepareValue($context));
    self::assertEquals(
        'default',
        $propA->prepareValue($context)->getValue(),
        "VariableProperties should return the default value if it's missing from the context.",
    );

    $propB = new VariableProperty($var, 'one', 'FAIL');
    self::assertEquals(
        'Foo',
        $propB->prepareValue($context)->getValue(),
    );
});

test('fluent interface helpers and anonymous variables', function (): void {
    $context = new Context([
        'root' => [
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
            'e' => 'string',
            'f' => 'ring',
            'g' => 'stuff',
            'h' => 'STRING',
        ],
    ]);

    $root = new Variable(new RuleBuilder(), 'root');

    $varA = $root['a'];
    $varB = $root['b'];
    $varC = $root['c'];
    $varD = $root['d'];
    $varE = $root['e'];
    $varF = $root['f'];
    $varG = $root['g'];
    $varH = $root['h'];

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

    self::assertFalse($varA->greaterThan($varB)->evaluate($context));
    self::assertTrue($varA->lessThan($varB)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\StringContains::class, $varE->stringContains('ring'));
    self::assertTrue($varE->stringContains($varF)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\StringContainsInsensitive::class, $varE->stringContainsInsensitive('STRING'));
    self::assertTrue($varE->stringContainsInsensitive($varH)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\StringDoesNotContain::class, $varE->stringDoesNotContain('cheese'));
    self::assertTrue($varE->stringDoesNotContain($varG)->evaluate($context));

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

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\EndsWith::class, $varE->endsWith('string'));
    self::assertTrue($varE->endsWith($varE)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\EndsWithInsensitive::class, $varE->endsWithInsensitive('STRING'));
    self::assertTrue($varE->endsWithInsensitive($varE)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\StartsWith::class, $varE->startsWith('string'));
    self::assertTrue($varE->startsWith($varE)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\StartsWithInsensitive::class, $varE->startsWithInsensitive('STRING'));
    self::assertTrue($varE->startsWithInsensitive($varE)->evaluate($context));
});
