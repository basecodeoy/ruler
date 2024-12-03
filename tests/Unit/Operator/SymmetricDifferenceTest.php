<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\SymmetricDifference;
use BaseCodeOy\Ruler\Variable;

dataset('provideSymmetric_differenceCases', function () {
    yield from [
        [6, 2, [6, 2]],
        [
            ['a', 'b', 'c'],
            'a',
            ['b', 'c'],
        ],
        [
            'a',
            ['a', 'b', 'c'],
            ['b', 'c'],
        ],
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
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['a'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['c', 'd'],
        ],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new SymmetricDifference($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\VariableOperand::class, $op);
});

test('invalid data', function (): void {
    $varA = new Variable('a', 'string');
    $varB = new Variable('b', 'blah');
    $context = new Context();

    $op = new SymmetricDifference($varA, $varB);
    self::assertEquals(
        ['string', 'blah'],
        $op->prepareValue($context)->getValue(),
    );
});

test('symmetric difference', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new SymmetricDifference($varA, $varB);
    self::assertEquals(
        $result,
        $op->prepareValue($context)->getValue(),
    );
})->with('provideSymmetric_differenceCases');
