<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Variable;
use BaseCodeOy\Ruler\VariableProperty;

test('constructor', function (): void {
    $name = 'evil';
    $prop = new VariableProperty(new Variable(), $name);
    self::assertEquals($name, $prop->getName());
    self::assertNull($prop->getValue());
});

test('get set value', function (): void {
    $values = \explode(', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it');

    $prop = new VariableProperty(new Variable(), 'technologic');

    foreach ($values as $valueString) {
        $prop->setValue($valueString);
        self::assertEquals($valueString, $prop->getValue());
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

    $var = new Variable('root');

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
