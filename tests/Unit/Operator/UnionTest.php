<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\Union;
use BaseCodeOy\Ruler\Variable;

dataset('provideUnionCases', function () {
    yield from [
        [6, 2, [6, 2]],
        [
            'a',
            ['b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['a', 'b', 'c'],
            [],
            ['a', 'b', 'c'],
        ],
        [
            [],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            [],
            [],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['a', 'b', 'c', 'd', 'e', 'f'],
        ],
        [
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['b', 'c', 'd'],
        ],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new Union($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (): void {
    $varA = new Variable('a', 'string');
    $varB = new Variable('b', 'blah');
    $context = new Context();

    $op = new Union($varA, $varB);
    self::assertEquals(
        [
            'string',
            'blah',
        ],
        $op->prepareValue($context)->getValue(),
    );
});

test('union', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new Union($varA, $varB);
    self::assertEquals($op->prepareValue($context)->getValue(), $result);
})->with('provideUnionCases');
