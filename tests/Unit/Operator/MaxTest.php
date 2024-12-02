<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Max;
use BaseCodeOy\Ruler\Variable;

dataset('provideInvalid_dataCases', function () {
    yield from [
        ['string'],
        [['string']],
        [[1, 2, 3, 'string']],
        [['string', 1, 2, 3]],
    ];
});
dataset('provideMaxCases', function () {
    yield from [
        [5, 5],
        [[], null],
        [[5], 5],
        [[-2, -5, -242], -2],
        [[2, 5, 242], 242],
    ];
});

test('interface', function (): void {
    $var = new Variable('a', [5, 2, 9]);

    $op = new Max($var);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (mixed $datum): void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('max: all values must be numeric');
    $var = new Variable('a', $datum);
    $context = new Context();

    $op = new Max($var);
    $op->prepareValue($context);
})->with('provideInvalid_dataCases');

test('max', function (mixed $a, mixed $result): void {
    $var = new Variable('a', $a);
    $context = new Context();

    $op = new Max($var);
    self::assertEquals(
        $result,
        $op->prepareValue($context)->getValue(),
    );
})->with('provideMaxCases');
