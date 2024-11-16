<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\Rule;
use BaseCodeOy\Ruler\RuleSet;
use Tests\Fixtures\TrueProposition;

test('ruleset creation update and execution', function (): void {
    $context = new Context();
    $true = new TrueProposition();

    $executedActionA = false;
    $ruleA = new Rule($true, function () use (&$executedActionA): void {
        $executedActionA = true;
    });

    $executedActionB = false;
    $ruleB = new Rule($true, function () use (&$executedActionB): void {
        $executedActionB = true;
    });

    $executedActionC = false;
    $ruleC = new Rule($true, function () use (&$executedActionC): void {
        $executedActionC = true;
    });

    $ruleset = new RuleSet([$ruleA]);

    $ruleset->executeRules($context);

    self::assertTrue($executedActionA);
    self::assertFalse($executedActionB);
    self::assertFalse($executedActionC);

    $ruleset->addRule($ruleC);
    $ruleset->executeRules($context);

    self::assertTrue($executedActionA);
    self::assertFalse($executedActionB);
    self::assertTrue($executedActionC);
});
