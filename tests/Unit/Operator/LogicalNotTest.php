<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Operator\LogicalNot;
use Tests\Fixtures\FalseProposition;
use Tests\Fixtures\TrueProposition;

test('interface', function (): void {
    $true = new TrueProposition();

    $op = new LogicalNot([$true]);
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $op);
});

test('constructor', function (): void {
    $op = new LogicalNot([new FalseProposition()]);
    self::assertTrue($op->evaluate(new Context()));
});

test('add proposition and evaluate', function (): void {
    $op = new LogicalNot();

    $op->addProposition(new TrueProposition());
    self::assertFalse($op->evaluate(new Context()));
});

test('executing a logical not without propositions throws an exception', function (): void {
    $this->expectException(LogicException::class);
    $op = new LogicalNot();
    $op->evaluate(new Context());
});

test('instantiating a logical not with too many arguments throws an exception', function (): void {
    $this->expectException(LogicException::class);
    $op = new LogicalNot([new TrueProposition(), new FalseProposition()]);
});

test('adding a second proposition to logical not throws an exception', function (): void {
    $this->expectException(LogicException::class);
    $op = new LogicalNot();
    $op->addProposition(new TrueProposition());
    $op->addProposition(new TrueProposition());
});
