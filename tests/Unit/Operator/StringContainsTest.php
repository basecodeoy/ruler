<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\StringContains;
use BaseCodeOy\Ruler\Operator\StringDoesNotContain;
use BaseCodeOy\Ruler\Variable;

dataset('containsData', function () {
    yield from [
        ['supercalifragilistic', 'super', true],
        ['supercalifragilistic', 'fragil', true],
        ['supercalifragilistic', 'a', true],
        ['supercalifragilistic', 'stic', true],
        ['timmy', 'bob', false],
        ['tim', 'TIM', false],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', [2]);

    $op = new StringContains($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('contains', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new StringContains($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('containsData');

test('does not contain', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new StringDoesNotContain($varA, $varB);
    self::assertNotEquals($op->evaluate($context), $result);
})->with('containsData');
