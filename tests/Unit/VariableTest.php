<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Variable;

test('constructor', function (): void {
    $name = 'evil';
    $var = new Variable($name);
    self::assertEquals($name, $var->getName());
    self::assertNull($var->getValue());
});

test('get set value', function (): void {
    $values = \explode(', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it');

    $variable = new Variable('technologic');

    foreach ($values as $valueString) {
        $variable->setValue($valueString);
        self::assertEquals($valueString, $variable->getValue());
    }
});

test('prepare value', function (): void {
    $values = [
        'one' => 'Foo',
        'two' => 'BAR',
        'three' => fn (): string => 'baz',
    ];

    $context = new Context($values);

    $varA = new Variable('four', 'qux');
    self::assertInstanceOf(BaseCodeOy\Ruler\Value::class, $varA->prepareValue($context));
    self::assertEquals(
        'qux',
        $varA->prepareValue($context)->getValue(),
        "Variables should return the default value if it's missing from the context.",
    );

    $varB = new Variable('one', 'FAIL');
    self::assertEquals(
        'Foo',
        $varB->prepareValue($context)->getValue(),
    );

    $varC = new Variable('three', 'FAIL');
    self::assertEquals(
        'baz',
        $varC->prepareValue($context)->getValue(),
    );

    $varD = new Variable(null, 'qux');
    self::assertInstanceOf(BaseCodeOy\Ruler\Value::class, $varD->prepareValue($context));
    self::assertEquals(
        'qux',
        $varD->prepareValue($context)->getValue(),
        "Anonymous variables don't require a name to prepare value",
    );
});
