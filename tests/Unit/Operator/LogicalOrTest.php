<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\LogicalOr;
use Tests\Fixtures\FalseProposition;
use Tests\Fixtures\TrueProposition;

test('interface', function (): void {
    $true = new TrueProposition();

    $op = new LogicalOr([$true]);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('constructor', function (): void {
    $true = new TrueProposition();
    $false = new FalseProposition();
    $context = new Context();

    $op = new LogicalOr([$true, $false]);
    self::assertTrue($op->evaluate($context));
});

test('add proposition and evaluate', function (): void {
    $true = new TrueProposition();
    $false = new FalseProposition();
    $context = new Context();

    $op = new LogicalOr();

    $op->addProposition($false);
    self::assertFalse($op->evaluate($context));

    $op->addProposition($false);
    self::assertFalse($op->evaluate($context));

    $op->addOperand($true);
    self::assertTrue($op->evaluate($context));
});

test('executing a logical or without propositions throws an exception', function (): void {
    $this->expectException(LogicException::class);
    $op = new LogicalOr();
    $op->evaluate(new Context());
});