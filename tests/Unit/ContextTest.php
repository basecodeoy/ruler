<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use Tests\Fixtures\Fact;
use Tests\Fixtures\Invokable;

/**
 * Provider for invalid fact definitions.
 */
dataset('badFactDefinitionProvider', [
    [123],
    [new Fact()],
]);

/**
 * Provider for fact definitions.
 */
dataset('factDefinitionProvider', [
    [
        fn (): \Closure => function ($value): Fact {
            $fact = new Fact();
            $fact->value = $value;

            return $fact;
        },
    ],
    [new Invokable()],
]);

test('constructor', function (): void {
    $facts = [
        'name' => 'Mint Chip',
        'type' => 'Ice Cream',
        'delicious' => fn (): true => true,
    ];

    $context = new Context($facts);

    self::assertArrayHasKey('name', $context);
    self::assertEquals('Mint Chip', $context['name']);

    self::assertArrayHasKey('type', $context);
    self::assertEquals('Ice Cream', $context['type']);

    self::assertArrayHasKey('delicious', $context);
    self::assertTrue($context['delicious']);
});

test('with string', function (): void {
    $context = new Context();
    $context['param'] = 'value';

    self::assertEquals('value', $context['param']);
});

test('with closure', function (): void {
    $context = new Context();
    $context['fact'] = fn (): \Tests\Fixtures\Fact => new Fact();

    self::assertInstanceOf(Fact::class, $context['fact']);
});

test('facts should be different', function (): void {
    $context = new Context();
    $context['fact'] = fn (): \Tests\Fixtures\Fact => new Fact();

    $factOne = $context['fact'];
    self::assertInstanceOf(Fact::class, $factOne);

    $factTwo = $context['fact'];
    self::assertInstanceOf(Fact::class, $factTwo);

    self::assertNotSame($factOne, $factTwo);
});

test('should pass context as parameter', function (): void {
    $context = new Context();
    $context['fact'] = fn (): \Tests\Fixtures\Fact => new Fact();
    $context['context'] = fn ($context) => $context;

    self::assertNotSame($context, $context['fact']);
    self::assertSame($context, $context['context']);
});

test('isset', function (): void {
    $context = new Context();
    $context['param'] = 'value';
    $context['fact'] = fn (): \Tests\Fixtures\Fact => new Fact();

    $context['null'] = null;

    self::assertArrayHasKey('param', $context);
    self::assertArrayHasKey('fact', $context);
    self::assertArrayHasKey('null', $context);
    self::assertArrayNotHasKey('non_existent', $context);
});

test('constructor injection', function (): void {
    $params = ['param' => 'value'];
    $context = new Context($params);

    self::assertSame($params['param'], $context['param']);
});

test('offset get validates key is present', function (): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Fact "foo" is not defined.');
    $context = new Context();
    echo $context['foo'];
});

test('offset get honors null values', function (): void {
    $context = new Context();
    $context['foo'] = null;
    self::assertNull($context['foo']);
});

test('unset', function (): void {
    $context = new Context();
    $context['param'] = 'value';
    $context['fact'] = fn (): \Tests\Fixtures\Fact => new Fact();

    unset($context['param'], $context['fact']);
    self::assertArrayNotHasKey('param', $context);
    self::assertArrayNotHasKey('fact', $context);
});

test('share', function (mixed $fact): void {
    $context = new Context();
    $context['shared_fact'] = $context->share($fact);

    $factOne = $context['shared_fact'];
    self::assertInstanceOf(Fact::class, $factOne);

    $factTwo = $context['shared_fact'];
    self::assertInstanceOf(Fact::class, $factTwo);

    self::assertSame($factOne, $factTwo);
})->with('factDefinitionProvider');

test('protect', function (mixed $fact): void {
    $context = new Context();
    $context['protected'] = $context->protect($fact);

    self::assertSame($fact, $context['protected']);
})->with('factDefinitionProvider');

test('global function name as parameter value', function (): void {
    $context = new Context();
    $context['global_function'] = 'strlen';
    self::assertSame('strlen', $context['global_function']);
});

test('raw', function (): void {
    $context = new Context();
    $context['fact'] = $definition = fn (): string => 'foo';
    self::assertSame($definition, $context->raw('fact'));
});

test('raw honors null values', function (): void {
    $context = new Context();
    $context['foo'] = null;
    self::assertNull($context->raw('foo'));
});

test('raw validates key is present', function (): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Fact "foo" is not defined.');
    $context = new Context();
    $context->raw('foo');
});

test('keys', function (): void {
    $context = new Context();
    $context['foo'] = 123;
    $context['bar'] = 123;

    self::assertEquals(['foo', 'bar'], $context->keys());
});

test('setting an invokable object should treat it as factory', function (): void {
    $context = new Context();
    $context['invokable'] = new Invokable();

    self::assertInstanceOf(Fact::class, $context['invokable']);
});

test('setting non invokable object should treat it as parameter', function (): void {
    $context = new Context();
    $context['non_invokable'] = new Fact();

    self::assertInstanceOf(Fact::class, $context['non_invokable']);
});

test('share fails for invalid fact definitions', function (mixed $fact): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Value is not a Closure or invokable object.');
    $context = new Context();
    $context->share($fact);
})->with('badFactDefinitionProvider');

test('protect fails for invalid fact definitions', function (mixed $fact): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Callable is not a Closure or invokable object.');
    $context = new Context();
    $context->protect($fact);
})->with('badFactDefinitionProvider');
