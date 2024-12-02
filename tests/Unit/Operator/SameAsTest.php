<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\SameAs;
use BaseCodeOy\Ruler\Variable;

test('interface', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', 1);

    $op = new SameAs($varA, $varB);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('constructor and evaluation', function (): void {
    $varA = new Variable('a', 1);
    $varB = new Variable('b', 2);
    $context = new Context();

    $op = new SameAs($varA, $varB);
    self::assertFalse($op->evaluate($context));

    $context['a'] = 2;
    self::assertTrue($op->evaluate($context));

    $context['a'] = 3;
    $context['b'] = fn (): int => 3;
    self::assertTrue($op->evaluate($context));

    $context['a'] = 3;
    $context['b'] = '3';
    self::assertFalse($op->evaluate($context));

    $context['a'] = new stdClass();
    $context['a']->attributes = 1;
    $context['b'] = new stdClass();
    $context['b']->attributes = 1;
    self::assertFalse($op->evaluate($context));

    $context['b'] = $context['a'];
    self::assertTrue($op->evaluate($context));

    $context['a'] = 1;
    $context['b'] = true;
    self::assertFalse($op->evaluate($context));

    $context['a'] = null;
    $context['b'] = false;
    self::assertFalse($op->evaluate($context));
});
