<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Negation;
use BaseCodeOy\Ruler\Variable;

dataset('provideSubtractCases', function () {
    yield from [
        [1, -1],
        [0.0, 0.0],
        ['0', 0],
        [-62_834, 62_834],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);

    $op = new Negation($varA);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Arithmetic: values must be numeric');
    $varA = new Variable('a', 'string');
    $context = new Context();

    $op = new Negation($varA);
    $op->prepareValue($context);
});

test('subtract', function (mixed $a, mixed $result): void {
    $varA = new Variable('a', $a);
    $context = new Context();

    $op = new Negation($varA);
    self::assertEquals($op->prepareValue($context)->getValue(), $result);
})->with('provideSubtractCases');
