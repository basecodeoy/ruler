<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BaseCodeOy\Ruler\Context;
use BaseCodeOy\Ruler\RuleBuilder;
use BaseCodeOy\Ruler\RuleBuilder\Variable;
use Tests\Fixtures\FalseProposition;
use Tests\Fixtures\TrueProposition;

test('interface', function (): void {
    $rb = new RuleBuilder();
    self::assertInstanceOf(RuleBuilder::class, $rb);
    self::assertInstanceOf(ArrayAccess::class, $rb);
});

test('manipulate variables via array access', function (): void {
    $name = 'alpha';
    $rb = new RuleBuilder();

    self::assertArrayNotHasKey($name, $rb);

    $var = $rb[$name];
    self::assertArrayHasKey($name, $rb);

    self::assertInstanceOf(BaseCodeOy\Ruler\AbstractVariable::class, $var);
    self::assertInstanceOf(Variable::class, $var);
    self::assertEquals($name, $var->getName());

    self::assertSame($var, $rb[$name]);
    self::assertNull($var->getValue());

    $rb[$name] = 'eeesh.';
    self::assertEquals('eeesh.', $var->getValue());

    unset($rb[$name]);
    self::assertArrayNotHasKey($name, $rb);
    self::assertNotSame($var, $rb[$name]);
});

test('logical operator generation', function (): void {
    $rb = new RuleBuilder();
    $context = new Context();

    $true = new TrueProposition();
    $false = new FalseProposition();

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LogicalAnd::class, $rb->logicalAnd($true, $false));
    self::assertFalse($rb->logicalAnd($true, $false)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LogicalOr::class, $rb->logicalOr($true, $false));
    self::assertTrue($rb->logicalOr($true, $false)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LogicalNot::class, $rb->logicalNot($true));
    self::assertFalse($rb->logicalNot($true)->evaluate($context));

    self::assertInstanceOf(BaseCodeOy\Ruler\Operator\LogicalXor::class, $rb->logicalXor($true, $false));
    self::assertTrue($rb->logicalXor($true, $false)->evaluate($context));
});

test('rule creation', function (): void {
    $rb = new RuleBuilder();
    $context = new Context();

    $true = new TrueProposition();
    $false = new FalseProposition();

    self::assertInstanceOf(BaseCodeOy\Ruler\Rule::class, $rb->create($true));
    self::assertTrue($rb->create($true)->evaluate($context));
    self::assertFalse($rb->create($false)->evaluate($context));

    $executed = false;
    $rule = $rb->create($true, function () use (&$executed): void {
        $executed = true;
    });

    self::assertFalse($executed);
    $rule->execute($context);
    self::assertTrue($executed);
});

test('not add equal to', function (): void {
    $rb = new RuleBuilder();
    $context = new Context([
        'A2' => 8,
        'A3' => 4,
        'B2' => 13,
    ]);

    $rule = $rb->logicalNot(
        $rb['A2']->equalTo($rb['B2']),
    );
    self::assertTrue($rule->evaluate($context));

    $rule = $rb['A2']->add($rb['A3']);

    $rule = $rb->logicalNot(
        $rule->equalTo($rb['B2']),
    );
    self::assertTrue($rule->evaluate($context));
});

test('external operators', function (): void {
    $rb = new RuleBuilder();
    $rb->registerOperatorNamespace('\Tests\Fixtures');

    $context = new Context(['a' => 100]);
    $varA = $rb['a'];

    self::assertTrue($varA->aLotGreaterThan(1)->evaluate($context));

    $context['a'] = 9;
    self::assertFalse($varA->aLotGreaterThan(1)->evaluate($context));
});

test('logic exception on unknown operator', function (): void {
    $this->expectException(LogicException::class);
    $this->expectExceptionMessage('Unknown operator: "aLotBiggerThan"');
    $rb = new RuleBuilder();
    $rb->registerOperatorNamespace('\Tests\Fixtures');

    $varA = $rb['a'];

    $varA->aLotBiggerThan(1);
});
