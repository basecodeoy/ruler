<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Modulo;
use BaseCodeOy\Ruler\Variable;

dataset('provideModuloCases', function () {
    yield from [
        [6, 2, 0],
        [7, 3, 1],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new Modulo($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Arithmetic: values must be numeric');
    $varA = new Variable('a', 'string');
    $varB = new Variable('b', 'blah');
    $context = new Context();

    $op = new Modulo($varA, $varB);
    $op->prepareValue($context);
});

test('divide by zero', function (): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Division by zero');
    $varA = new Variable('a', \random_int(1, 100));
    $varB = new Variable('b', 0);
    $context = new Context();

    $op = new Modulo($varA, $varB);
    $op->prepareValue($context);
});

test('modulo', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new Modulo($varA, $varB);
    self::assertEquals($op->prepareValue($context)->getValue(), $result);
})->with('provideModuloCases');
