<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\RuleBuilder;

dataset('truthTableOne', function () {
    yield from [
        [true],
        [false],
    ];
});
dataset('truthTableTwo', function () {
    yield from [
        [true,  true],
        [true,  false],
        [false, true],
        [false, false],
    ];
});
dataset('truthTableThree', function () {
    yield from [
        [true,  true,  true],
        [true,  true,  false],
        [true,  false, true],
        [true,  false, false],
        [false, true,  true],
        [false, true,  false],
        [false, false, true],
        [false, false, false],
    ];
});

test('de morgan', function (mixed $p, mixed $q): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q]);
    self::assertEquals(
        $rb->create(
            $rb->logicalNot(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['p']->equalTo(true),
                ),
                $rb->logicalNot(
                    $rb['q']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableTwo');

test('de morgan two', function (mixed $p, mixed $q): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q]);
    self::assertEquals(
        $rb->create(
            $rb->logicalNot(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalAnd(
                $rb->logicalNot(
                    $rb['p']->equalTo(true),
                ),
                $rb->logicalNot(
                    $rb['q']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableTwo');

test('commutation', function (mixed $p, mixed $q): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q]);
    self::assertEquals(
        $rb->create(
            $rb->logicalOr(
                $rb['p']->equalTo(true),
                $rb['q']->equalTo(true),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalOr(
                $rb['q']->equalTo(true),
                $rb['p']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableTwo');

test('commutation two', function (mixed $p, mixed $q): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q]);
    self::assertEquals(
        $rb->create(
            $rb->logicalAnd(
                $rb['p']->equalTo(true),
                $rb['q']->equalTo(true),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalAnd(
                $rb['q']->equalTo(true),
                $rb['p']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableTwo');

test('association', function (mixed $p, mixed $q, mixed $r): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q, 'r' => $r]);
    self::assertEquals(
        $rb->create(
            $rb->logicalOr(
                $rb['p']->equalTo(true),
                $rb->logicalOr(
                    $rb['q']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalOr(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
                $rb['r']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableThree');

test('association two', function (mixed $p, mixed $q, mixed $r): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q, 'r' => $r]);
    self::assertEquals(
        $rb->create(
            $rb->logicalAnd(
                $rb['p']->equalTo(true),
                $rb->logicalAnd(
                    $rb['q']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalAnd(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
                $rb['r']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableThree');

test('distribution', function (mixed $p, mixed $q, mixed $r): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q, 'r' => $r]);
    self::assertEquals(
        $rb->create(
            $rb->logicalAnd(
                $rb['p']->equalTo(true),
                $rb->logicalOr(
                    $rb['q']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalOr(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableThree');

test('distribution two', function (mixed $p, mixed $q, mixed $r): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p, 'q' => $q, 'r' => $r]);
    self::assertEquals(
        $rb->create(
            $rb->logicalOr(
                $rb['p']->equalTo(true),
                $rb->logicalAnd(
                    $rb['q']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
        $rb->create(
            $rb->logicalAnd(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true),
                ),
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['r']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableThree');

test('double negation', function (mixed $p): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p]);
    self::assertEquals(
        $rb->create(
            $rb['p']->equalTo(true),
        )->evaluate($context),
        $rb->create(
            $rb->logicalNot(
                $rb->logicalNot(
                    $rb['p']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableOne');

test('tautology', function (mixed $p): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p]);
    self::assertEquals(
        $rb->create(
            $rb['p']->equalTo(true),
        )->evaluate($context),
        $rb->create(
            $rb->logicalOr(
                $rb['p']->equalTo(true),
                $rb['p']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableOne');

test('tautology two', function (mixed $p): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p]);
    self::assertEquals(
        $rb->create(
            $rb['p']->equalTo(true),
        )->evaluate($context),
        $rb->create(
            $rb->logicalAnd(
                $rb['p']->equalTo(true),
                $rb['p']->equalTo(true),
            ),
        )->evaluate($context),
    );
})->with('truthTableOne');

test('excluded middle', function (mixed $p): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p]);
    self::assertEquals(
        true,
        $rb->create(
            $rb->logicalOr(
                $rb['p']->equalTo(true),
                $rb->logicalNot(
                    $rb['p']->equalTo(true),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableOne');

test('non contradiction', function (mixed $p): void {
    $rb = new RuleBuilder();
    $context = new Context(['p' => $p]);
    self::assertEquals(
        true,
        $rb->create(
            $rb->logicalNot(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb->logicalNot(
                        $rb['p']->equalTo(true),
                    ),
                ),
            ),
        )->evaluate($context),
    );
})->with('truthTableOne');
