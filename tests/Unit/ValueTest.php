<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Value;

dataset('provideGreater_than_equal_to_and_less_thanCases', function () {
    yield from [
        [1, 2,     false, false, true],
        [2, 1,     true, false, false],
        [1, 1,     false, true, false],
        ['a', 'b', false, false, true],
        [
            Carbon\CarbonImmutable::now()->subDays(5),
            Carbon\CarbonImmutable::now()->addDays(5),
            false, false, true,
        ],
    ];
});

test('constructor', function (): void {
    $valueString = 'technologic';
    $value = new Value($valueString);
    self::assertEquals($valueString, $value->getValue());
});

test('greater than equal to and less than', function (mixed $a, mixed $b, mixed $gt, mixed $eq, mixed $lt): void {
    $valA = new Value($a);
    $valB = new Value($b);

    self::assertEquals($gt, $valA->greaterThan($valB));
    self::assertEquals($lt, $valA->lessThan($valB));
    self::assertEquals($eq, $valA->equalTo($valB));
})->with('provideGreater_than_equal_to_and_less_thanCases');
