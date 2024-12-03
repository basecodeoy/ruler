<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Rule;
use Tests\Fixtures\CallbackProposition;
use Tests\Fixtures\TrueProposition;

test('interface', function (): void {
    $rule = new Rule(new TrueProposition());
    self::assertInstanceOf(BaseCodeOy\Ruler\Proposition::class, $rule);
});

test('constructor evaluation and execution', function (): void {
    $test = $this;
    $context = new Context();
    $executed = false;
    $actionExecuted = false;

    $ruleOne = new Rule(
        new CallbackProposition(function ($c) use ($test, $context, &$executed, &$actionExecuted): false {
            $test->assertSame($c, $context);
            $executed = true;

            return false;
        }),
        function () use (&$actionExecuted): void {
            $actionExecuted = true;
        },
    );

    self::assertFalse($ruleOne->evaluate($context));
    self::assertTrue($executed);

    $ruleOne->execute($context);
    self::assertFalse($actionExecuted);

    $executed = false;
    $actionExecuted = false;

    $ruleTwo = new Rule(
        new CallbackProposition(function ($c) use ($test, $context, &$executed, &$actionExecuted): true {
            $test->assertSame($c, $context);
            $executed = true;

            return true;
        }),
        function () use (&$actionExecuted): void {
            $actionExecuted = true;
        },
    );

    self::assertTrue($ruleTwo->evaluate($context));
    self::assertTrue($executed);

    $ruleTwo->execute($context);
    self::assertTrue($actionExecuted);
});

test('non callable actions will throw an exception', function (): void {
    $this->expectException(LogicException::class);
    $context = new Context();
    $rule = new Rule(new TrueProposition(), 'this is not callable');
    $rule->execute($context);
});
