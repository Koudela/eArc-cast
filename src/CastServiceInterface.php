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

interface CastServiceInterface
{
    /**
     * TODO: Description
     *
     * @param array|object $origin
     * @param string|object $target
     * @param array|null $mapping
     *
     * @return object
     */
    public function cast($origin, $target, &$mapping = null);

    /**
     * TODO: Description
     *
     * @param object $object
     *
     * @return array|object
     */
    public function castReverse($object);

    /**
     * TODO: Description
     *
     * @param array|object $origin
     * @param string|array|object $target
     * @param array|null $mapping
     *
     * @return array|object
     */
    public function castSimple($origin, $target, &$mapping = null);
}
