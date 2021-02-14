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

class OriginParent
{
    public string $a = 'parent_A_origin';
    protected string $b = 'parent_B_origin';
    private string $c = 'parent_C_origin';
    public string $d = 'parent_D_origin';
    protected string $e = 'parent_E_origin';
    private string $f = 'parent_F_origin';
}
