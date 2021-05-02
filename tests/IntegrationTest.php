<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 * cast component
 *
 * @package earc/cast
 * @link https://github.com/Koudela/eArc-cast/
 * @copyright Copyright (c) 2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\CastTests;

use eArc\Cast\CastService;
use eArc\Cast\Initializer;
use PHPUnit\Framework\TestCase;
use function eArc\Cast\cast;
use function eArc\Cast\cast_reverse;
use function eArc\Cast\cast_simple;
use function eArc\Cast\generate_mapping;

class IntegrationTest extends TestCase
{
    const COMPLETE_OBJECT_MAP = [
        'a' => null,
        'b' => null,
        'c' => null,
        'd' => null,
        'e' => null,
        'f' => null,
        'g' => null,
        'h' => null,
        'i' => null,
    ];

    public function testArrayToArray()
    {
        Initializer::init();

        $origin = [10, 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];
        $target = [1, 2, 3, 'd' => 'X'];

        $result = cast_simple($origin, $target);
        self::assertEquals([10, 2, 3, 'd' => 'D'], $result);

        $mapping = ['a' => null, 'c' => 'c', 'b' => 'd'];

        $result = cast_simple($origin, $target, $mapping);
        self::assertEquals([1, 2, 3, 'a' => 'A', 'd' => 'B', 'c' => 'C'], $result);
    }

    public function testObjectToObject()
    {
        Initializer::init();

        $origin = new Origin();
        $mapping = self::COMPLETE_OBJECT_MAP;
        $expectedResult = [
            'a' => 'A_origin',
            'b' => 'B_origin',
            'c' => 'C_origin',
            'd' => 'parent_D_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
        ];
        $result = cast_simple($origin, [], $mapping);
        self::assertEquals($expectedResult, $result);

        $target = new Target();
        $expectedResult = [
            'd' => 'parent_D_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
            'g' => 'parent_G_target',
            'h' => 'parent_H_target',
            'i' => 'parent_I_target',
        ];

        $result = cast_simple($origin, $target);
        self::assertTrue($result instanceof Target);
        self::assertEquals($expectedResult, cast_simple($target, [], $mapping));

        $target = cast($origin, Target::class);
        self::assertTrue($target instanceof Target);
        self::assertEquals($expectedResult, cast_simple($target, [], $mapping));

        $target = cast_simple([
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'd' => 'D',
            'e' => 'E',
            'f' => 'F',
            'g' => 'G',
            'h' => 'H',
            'i' => 'I',
            ], $target);
        self::assertEquals([
            'd' => 'D',
            'e' => 'E',
            'f' => 'F',
            'g' => 'G',
            'h' => 'H',
            'i' => 'I',
        ], cast_simple($target, [], $mapping));

        $origin = cast_reverse($target);
        self::assertTrue($origin instanceof Origin);
        self::assertEquals([
            'a' => 'A_origin',
            'b' => 'B_origin',
            'c' => 'C_origin',
            'd' => 'D',
            'e' => 'E',
            'f' => 'F',
        ], cast_simple($origin, [], $mapping));
    }

    public function testCastReverse()
    {
        Initializer::init(new CastService());

        $arrayOrigin = [
            'a' => 'A_array_origin',
            'b' => 'B_array_origin',
            'c' => 'C_array_origin',
            'd' => 'D_array_origin',
            'x' => 'X_array_origin',
        ];
        $origin = cast($arrayOrigin, Origin::class);
        self::assertTrue($origin instanceof Origin);
        self::assertEquals([
            'a' => 'A_array_origin',
            'b' => 'B_array_origin',
            'c' => 'C_array_origin',
            'd' => 'D_array_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
        ], cast_simple($origin, [], self::COMPLETE_OBJECT_MAP));

        $target = new Target();
        $mapping = ['a' => 'd', 'b' => 'e', 'c' => 'f'];
        cast($origin, $target, $mapping);
        self::assertTrue($target instanceof Target);
        self::assertEquals([
            'd' => 'A_array_origin',
            'e' => 'B_array_origin',
            'f' => 'C_array_origin',
            'g' => 'parent_G_target',
            'h' => 'parent_H_target',
            'i' => 'parent_I_target',
        ], cast_simple($target, [], self::COMPLETE_OBJECT_MAP));
        cast_simple([
            'd' => 'A_origin_manipulated',
            'e' => 'B_origin_manipulated',
            'f' => 'C_origin_manipulated',
        ], $target);

        $origin = cast_reverse($target);
        self::assertTrue($origin instanceof Origin);
        self::assertEquals([
            'a' => 'A_origin_manipulated',
            'b' => 'B_origin_manipulated',
            'c' => 'C_origin_manipulated',
            'd' => 'D_array_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
        ], cast_simple($origin, [], self::COMPLETE_OBJECT_MAP));

        $arrayOrigin = cast_reverse($origin);
        self::assertTrue(is_array($arrayOrigin));
        self::assertEquals([
            'a' => 'A_origin_manipulated',
            'b' => 'B_origin_manipulated',
            'c' => 'C_origin_manipulated',
            'd' => 'D_array_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
            'x' => 'X_array_origin',
        ], $arrayOrigin);
    }

    public function testObjectToArray()
    {
        Initializer::init();

        $origin = new Origin();
        $target = ['d' => 1, 'e' => 2, 'f' => 3, 'g' => 4, 'h' => 5, 'i' => 6, 'j' => 7];
        $result = cast_simple($origin, $target, ['a' => null, 'b' => 'i', 'c' => 'z']);
        self::assertEquals([
            'a' => 'A_origin',
            'd' => 1,
            'e' => 2,
            'f' => 3,
            'g' => 4,
            'h' => 5,
            'i' => 'B_origin',
            'j' => 7,
            'z' => 'C_origin',
        ], $result);
    }

    public function testArrayToObject()
    {
        Initializer::init();

        $origin = ['d' => 1, 'e' => 2, 'f' => 3, 'g' => 4, 'h' => 5, 'i' => 6, 'j' => 7];
        $target = new Origin();
        $result = cast_simple($origin, $target, ['a' => null, 'd' => 'i', 'c' => 'z', 'e' => null, 'j' => 'f']);
        self::assertTrue($result instanceof Origin);
        self::assertEquals([
            'a' => 'A_origin',
            'b' => 'B_origin',
            'c' => 'C_origin',
            'd' => 'parent_D_origin',
            'e' => '2',
            'f' => '7',
        ], cast_simple($result, [], self::COMPLETE_OBJECT_MAP));

        $origin = ['d' => 1, 'e' => 2, 'f' => 3, 'g' => 4, 'h' => 5, 'i' => 6, 'j' => 7];
        $target = new Origin();
        $result = cast_simple($origin, $target);
        self::assertTrue($result instanceof Origin);
        self::assertEquals([
            'a' => 'A_origin',
            'b' => 'B_origin',
            'c' => 'C_origin',
            'd' => '1',
            'e' => '2',
            'f' => '3',
        ], cast_simple($result, [], self::COMPLETE_OBJECT_MAP));
    }

    public function testGenerateMapping()
    {
        Initializer::init();

        $origin = new Origin();
        $target = [];
        $result = cast_simple($origin, $target);
        self::assertEquals([], $result);

        $result = cast_simple($origin, $target, generate_mapping($origin));
        self::assertEquals([
            'a' => 'A_origin',
            'b' => 'B_origin',
            'c' => 'C_origin',
            'd' => 'parent_D_origin',
            'e' => 'parent_E_origin',
            'f' => 'parent_F_origin',
        ], $result);
    }
}
