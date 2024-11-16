<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\StartsWith;
use BaseCodeOy\Ruler\Variable;

dataset('provideStarts_withCases', function () {
    yield from [
        ['supercalifragilistic', 'supercalifragilistic', true],
        ['supercalifragilistic', 'super', true],
        ['supercalifragilistic', 'SUPER', false],
        ['supercalifragilistic', 'stic', false],
        ['supercalifragilistic', '', false],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 'foo bar baz');
    $varB = new Variable('b', 'foo');

    $op = new StartsWith($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('starts with', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new StartsWith($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('provideStarts_withCases');
