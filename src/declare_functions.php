<?php
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

if (!function_exists('\eArc\Cast\cast')) {
    function cast($origin, $target, $mapping = null)
    {
        return Initializer::$castService->cast($origin, $target, $mapping);
    }
}

if (!function_exists('\eArc\Cast\cast_reverse')) {
    function cast_reverse($object)
    {
        return Initializer::$castService->castReverse($object);
    }
}

if (!function_exists('\eArc\Cast\cast_simple')) {
    function cast_simple($origin, $target, $mapping = null)
    {
        return Initializer::$castService->castSimple($origin, $target, $mapping);
    }
}
