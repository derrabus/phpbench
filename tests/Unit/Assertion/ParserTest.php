<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PhpBench\Tests\Unit\Assertion;

use PHPUnit\Framework\TestCase;
use Generator;
use PhpBench\Assertion\Ast\Comparator;
use PhpBench\Assertion\Ast\Condition;
use PhpBench\Assertion\Ast\Number;
use PhpBench\Assertion\Ast\PropertyAccess;
use PhpBench\Assertion\Ast\Unit;
use PhpBench\Assertion\Ast\Value;
use PhpBench\Assertion\Ast\Variable;
use PhpBench\Assertion\Ast\Within;
use PhpBench\Assertion\Parser;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit as SebastianBergmannUnit;

class ParserTest extends TestCase
{
    /**
     * @dataProvider provideGeneral
     * @dataProvider provideTimeUnits
     */
    public function testParse(string $dsl, Condition $expected): void
    {
        $ast = (new Parser())->parse($dsl);
        $this->assertEquals($expected, $ast);
    }
    
    /**
     * @return Generator<mixed>
     */
    public function provideGeneral(): Generator
    {
        yield 'within' => [
            'this.mean within 10% of baseline.mean',
            new Condition(
                new PropertyAccess(['this', 'mean']),
                new Within(
                    new Value(new Number(10), new Unit('%'))
                ),
                new PropertyAccess(['baseline', 'mean'])
            ),
        ];

        yield [
            'this.mean WITHIN 10% OF baseline.mean',
            new Condition(
                new PropertyAccess(['this', 'mean']),
                new Within(
                    new Value(new Number(10), new Unit('%'))
                ),
                new PropertyAccess(['baseline', 'mean'])
            )
        ];

        yield [
            'this.mem_peak less than 100 microseconds',
            new Condition(
                new PropertyAccess(['this', 'mem_peak']),
                new Comparator('less than'),
                new Value(new Number(100), new Unit('microseconds'))
            )
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideTimeUnits(): Generator
    {
        yield [
            'this.mem_peak less than 100 microseconds',
            new Condition(
                new PropertyAccess(['this', 'mem_peak']),
                new Comparator('less than'),
                new Value(new Number(100), new Unit('microseconds'))
            )
        ];

        yield [
            'this.mem_peak less than 100 milliseconds',
            new Condition(
                new PropertyAccess(['this', 'mem_peak']),
                new Comparator('less than'),
                new Value(new Number(100), new Unit('milliseconds'))
            )
        ];

        yield [
            'this.mem_peak less than 100 seconds',
            new Condition(
                new PropertyAccess(['this', 'mem_peak']),
                new Comparator('less than'),
                new Value(new Number(100), new Unit('seconds'))
            )
        ];
    }


}

