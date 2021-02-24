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

class Initializer
{
    public static $castService;

    public static function init($castService = null)
    {
        self::initCastService($castService);

        include __DIR__.'/declare_functions.php';

        return self::$castService;
    }

    protected static function initCastService($castService)
    {
        self::$castService = !is_null($castService) ? $castService: (!is_null(self::$castService) ? self::$castService : new CastService());
    }
}
