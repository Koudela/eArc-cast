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

namespace eArc\Cast;

if (!function_exists('eArc\\Cast\\cast')) {
    function cast(array|object $origin, string|object $target, ?array $mapping = null): object
    {
        return Initializer::$castService->cast($origin, $target, $mapping);
    }
}

if (!function_exists('eArc\\Cast\\cast_reverse')) {
    function cast_reverse(object $object): array|object
    {
        return Initializer::$castService->castReverse($object);
    }
}

if (!function_exists('eArc\\Cast\\cast_simple')) {
    function cast_simple(array|object $origin, string|array|object $target, ?array $mapping = null): array|object
    {
        return Initializer::$castService->castSimple($origin, $target, $mapping);
    }
}

if (!function_exists('eArc\\Cast\\generate_mapping')) {
    function generate_mapping(string|array|object $target): array
    {
        return Initializer::$castService->generateMapping($target);
    }
}
