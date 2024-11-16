<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\EndsWithInsensitive;
use BaseCodeOy\Ruler\Operator\StartsWith;
use BaseCodeOy\Ruler\Variable;

dataset('provideEnds_with_insensitiveCases', function () {
    yield from [
        ['supercalifragilistic', 'supercalifragilistic', true],
        ['supercalifragilistic', 'stic', true],
        ['supercalifragilistic', 'STIC', true],
        ['supercalifragilistic', 'super', false],
        ['supercalifragilistic', '', false],
    ];
});

test('interface', function (): void {
    $varA = new Variable('a', 'foo bar baz');
    $varB = new Variable('b', 'foo');

    $op = new StartsWith($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('ends with insensitive', function (mixed $a, mixed $b, mixed $result): void {
    $varA = new Variable('a', $a);
    $varB = new Variable('b', $b);
    $context = new Context();

    $op = new EndsWithInsensitive($varA, $varB);
    self::assertEquals($op->evaluate($context), $result);
})->with('provideEnds_with_insensitiveCases');
