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

class Origin extends OriginParent
{
    public string $a = 'A_origin';
    protected string $b = 'B_origin';
    private string $c = 'C_origin';
}
