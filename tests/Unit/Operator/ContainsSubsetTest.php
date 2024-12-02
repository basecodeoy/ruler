<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\ContainsSubset;
use BaseCodeOy\Ruler\Operator\DoesNotContainSubset;
use BaseCodeOy\Ruler\Variable;

dataset('containsData', function () {
    yield from [
        [[1], [1], true],
        [[1], 1, true],
        [[1, 2, 3], [1, 2], true],
        [[1, 2, 3], [2, 4], false],
        [['foo', 'bar', 'baz'], ['pow'], false],
        [['foo', 'bar', 'baz'], ['bar'], true],
        [['foo', 'bar', 'baz'], ['bar', 'baz'], true],
        [null, 'bar', false],
        [null, ['bar'], false],
        [null, ['bar', 'baz'], false],
        [null, null, true],
        [[], [], true],
        [[1, 2, 3], [2], true],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new ContainsSubset($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('contains', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new ContainsSubset($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('containsData');

test('does not contain', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new DoesNotContainSubset($varA, $varB);
    self::assertNotEquals($op->evaluate($context), $result);
})->with('containsData');
