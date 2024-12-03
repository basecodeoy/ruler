<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\SetContains;
use BaseCodeOy\Ruler\Operator\SetDoesNotContain;
use BaseCodeOy\Ruler\Variable;

dataset('containsData', function () {
    yield from [
        [[1], 1, true],
        [[1, 2, 3], 1, true],
        [[1, 2, 3], 4, false],
        [['foo', 'bar', 'baz'], 'pow', false],
        [['foo', 'bar', 'baz'], 'bar', true],
        [null, 'bar', false],
        [null, null, false],
        [[1, 2, 3], [2], false],
        [[1, 2, ['foo']], ['foo'], true],
        [[1], [1], false],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new SetContains($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('contains', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new SetContains($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('containsData');

test('does not contain', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new SetDoesNotContain($varA, $varB);
    self::assertNotEquals($op->evaluate($context), $result);
})->with('containsData');
