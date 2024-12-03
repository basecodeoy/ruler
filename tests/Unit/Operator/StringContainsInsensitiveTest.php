<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\StringContainsInsensitive;
use BaseCodeOy\Ruler\Operator\StringDoesNotContainInsensitive;
use BaseCodeOy\Ruler\Variable;

dataset('containsData', function () {
    yield from [
        ['supercalifragilistic', 'super', true],
        ['supercalifragilistic', 'fragil', true],
        ['supercalifragilistic', 'a', true],
        ['supercalifragilistic', 'stic', true],
        ['timmy', 'bob', false],
        ['timmy', 'tim', true],
        ['supercalifragilistic', 'SUPER', true],
        ['supercalifragilistic', 'frAgil', true],
        ['supercalifragilistic', 'A', true],
        ['supercalifragilistic', 'sTiC', true],
        ['timmy', 'bob', false],
        ['timmy', 'TIM', true],
        ['tim', 'TIM', true],
        ['tim', 'TiM', true],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new StringContainsInsensitive($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('contains', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new StringContainsInsensitive($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('containsData');

test('does not contain', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new StringDoesNotContainInsensitive($varA, $varB);
    self::assertNotEquals($op->evaluate($context), $result);
})->with('containsData');
