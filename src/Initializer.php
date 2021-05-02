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

class Initializer
{
    public static CastServiceInterface|null $castService;

    public static function init(?CastServiceInterface $castService = null): CastServiceInterface
    {
        self::$castService = $castService ?? (self::$castService ?? new CastService());

        include __DIR__.'/declare_functions.php';

        return self::$castService;
    }
}
