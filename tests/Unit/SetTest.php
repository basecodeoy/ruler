<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Set;
use BaseCodeOy\Ruler\Value;
use Tests\Fixtures\toStringable;

test('non stringable object', function (): void {
    $setExpected = [
        new stdClass(),
        new stdClass(),
    ];
    $set = new Set($setExpected);
    self::assertCount(2, $set);
});

test('object uniqueness', function (): void {
    $objectA = new stdClass();
    $objectA->something = 'else';
    $objectB = new stdClass();
    $objectB->foo = 'bar';

    $set = new Set([
        $objectA,
        $objectB,
    ]);

    self::assertCount(2, $set);
    self::assertTrue($set->setContains(new Value($objectA)));
    self::assertTrue($set->setContains(new Value($objectB)));
    self::assertFalse($set->setContains(new Value(false)));
});

test('stringable', function (): void {
    $set = new Set([
        $one = new toStringable(1),
        $two = new toStringable(2),
        $too = new toStringable(2),
    ]);

    self::assertCount(2, $set);
    self::assertTrue($set->setContains(new Value($one)));
    self::assertTrue($set->setContains(new Value($two)));
    self::assertFalse($set->setContains(new Value($too)));
    self::assertFalse($set->setContains(new Value(2)));
});
