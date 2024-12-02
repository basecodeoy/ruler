<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\LessThan;
use BaseCodeOy\Ruler\Variable;

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', 2);

    $op = new LessThan($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('constructor and evaluation', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', 2);
    $context = new Context();

    $op = new LessThan($varA, $varB);
    self::assertTrue($op->evaluate($context));

    $context['a'] = 2;
    self::assertFalse($op->evaluate($context));

    $context['a'] = 3;
    $context['b'] = fn (): int => 1;
    self::assertFalse($op->evaluate($context));
});