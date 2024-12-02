<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\RuleBuilder;

dataset('provideUnionCases', function () {
    yield from [
        [
            ['a', 'b', 'c'],
            [],
            ['a', 'b', 'c'],
        ],
        [
            [],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            [],
            [],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['a', 'b', 'c', 'd', 'e', 'f'],
        ],
        [
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['b', 'c', 'd'],
        ],
    ];
});
dataset('provideIntersectCases', function () {
    yield from [
        [
            ['a', 'b', 'c'],
            [],
            [],
        ],
        [
            [],
            ['a', 'b', 'c'],
            [],
        ],
        [
            [],
            [],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['b', 'c'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['b'],
        ],
    ];
});
dataset('provideComplementCases', function () {
    yield from [
        [
            ['a', 'b', 'c'],
            [],
            ['a', 'b', 'c'],
        ],
        [
            [],
            ['a', 'b', 'c'],
            [],
        ],
        [
            [],
            [],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['a', 'b', 'c'],
        ],
        [
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['a'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['c'],
        ],
    ];
});
dataset('provideSymmetric_differenceCases', function () {
    yield from [
        [
            ['a', 'b', 'c'],
            [],
            ['a', 'b', 'c'],
        ],
        [
            [],
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
        ],
        [
            [],
            [],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['a', 'b', 'c', 'd', 'e', 'f'],
        ],
        [
            ['a', 'b', 'c'],
            ['a', 'b', 'c'],
            [],
        ],
        [
            ['a', 'b', 'c'],
            ['b', 'c'],
            ['a'],
        ],
        [
            ['b', 'c'],
            ['b', 'd'],
            ['c', 'd'],
        ],
    ];
});

test('complicated', function (): void {
    $rb = new RuleBuilder();
    $context = new Context([
        'expected' => 'a',
        'foo' => ['a', 'z'],
        'bar' => ['z', 'b'],
        'baz' => ['a', 'z', 'b', 'q'],
        'bob' => ['a', 'd'],
    ]);

    self::assertTrue(
        $rb->create(
            $rb['foo']->intersect(
                $rb['bar']->symmetricDifference($rb['baz']),
            )->setContains($rb['expected']),
        )->evaluate($context),
    );

    self::assertTrue(
        $rb->create(
            $rb['bar']->union(
                $rb['bob'],
            )->containsSubset($rb['foo']),
        )->evaluate($context),
    );
});

test('union', function (mixed $a, mixed $b, mixed $expected): void {
    $rb = new RuleBuilder();
    $context = new Context(['a' => $a, 'b' => $b, 'expected' => $expected]);
    self::assertTrue(
        $rb->create(
            $rb['expected']->equalTo(
                $rb['a']->union($rb['b']),
            ),
        )->evaluate($context),
    );
})->with('provideUnionCases');

test('intersect', function (mixed $a, mixed $b, mixed $expected): void {
    $rb = new RuleBuilder();
    $context = new Context(['a' => $a, 'b' => $b, 'expected' => $expected]);
    self::assertTrue(
        $rb->create(
            $rb['expected']->equalTo(
                $rb['a']->intersect($rb['b']),
            ),
        )->evaluate($context),
    );
})->with('provideIntersectCases');

test('complement', function (mixed $a, mixed $b, mixed $expected): void {
    $rb = new RuleBuilder();
    $context = new Context(['a' => $a, 'b' => $b, 'expected' => $expected]);
    self::assertTrue(
        $rb->create(
            $rb['expected']->equalTo(
                $rb['a']->complement($rb['b']),
            ),
        )->evaluate($context),
    );
})->with('provideComplementCases');

test('symmetric difference', function (mixed $a, mixed $b, mixed $expected): void {
    $rb = new RuleBuilder();
    $context = new Context(['a' => $a, 'b' => $b, 'expected' => $expected]);
    self::assertTrue(
        $rb->create(
            $rb['expected']->equalTo(
                $rb['a']->symmetricDifference($rb['b']),
            ),
        )->evaluate($context),
    );
})->with('provideSymmetric_differenceCases');
