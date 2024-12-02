<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Min;
use BaseCodeOy\Ruler\Variable;

dataset('provideInvalid_dataCases', function () {
    yield from [
        ['string'],
        [['string']],
        [[1, 2, 3, 'string']],
        [['string', 1, 2, 3]],
    ];
});
dataset('provideMinCases', function () {
    yield from [
        [5, 5],
        [[], null],
        [[5], 5],
        [[-2, -5, -242], -242],
        [[2, 5, 242], 2],
    ];
});

test('interface', function (): void {
    $var = new Variable('a', [5, 2, 9]);

    $op = new Min($var);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (mixed $datum): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('min: all values must be numeric');
    $var = new Variable('a', $datum);
    $context = new Context();

    $op = new Min($var);
    $op->prepareValue($context);
})->with('provideInvalid_dataCases');

test('min', function (mixed $a, mixed $result): void {
    $var = new Variable('a', $a);
    $context = new Context();

    $op = new Min($var);
    self::assertEquals(
        $result,
        $op->prepareValue($context)->getValue(),
    );
})->with('provideMinCases');
