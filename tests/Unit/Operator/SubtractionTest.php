<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Subtraction;
use BaseCodeOy\Ruler\Variable;

dataset('provideSubtractCases', function () {
    yield from [
        [6, 2, 4],
        [7, -3, 10],
        [2.5, 1.4, 1.1],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new Subtraction($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Arithmetic: values must be numeric');
    $varA = new Variable('a', 'string');
    $varB = new Variable('b', 'blah');
    $context = new Context();

    $op = new Subtraction($varA, $varB);
    $op->prepareValue($context);
});

test('subtract', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new Subtraction($varA, $varB);
    self::assertEquals($op->prepareValue($context)->getValue(), $result);
})->with('provideSubtractCases');
