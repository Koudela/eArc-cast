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

interface CastServiceInterface
{
    /**
     * Applies the properties/values of the origin object/array to the properties/values
     * of the target object/array. The identical keys/property names are used to
     * define a mapping if no mapping is supplied. If a mapping is supplied the
     * keys refer to the keys/property names of the origin, whereas the values
     * refer to the keys/property names of the target. If a mapping value is null,
     * the mapping key also defines the mapping value.
     *
     * A fully qualified class name can be used as target. In this case the target
     * object is initialized without constructor.
     *
     * @param array|object $origin
     * @param string|array|object $target
     * @param array|null $mapping
     *
     * @return array|object
     */
    public function castSimple(array|object $origin, string|array|object $target, ?array $mapping = null): array|object;

    /**
     * Cast does the same as simple cast but remembers the origin array/object
     * and the used mapping for the use in conjunction with `castReverse()`.
     *
     * @param array|object $origin
     * @param string|object $target
     * @param array|null $mapping
     *
     * @return object
     */
    public function cast(array|object $origin, string|object $target, ?array $mapping = null): object;

    /**
     * The remembered origin array/object is used to apply the target values back
     * to its origin.
     *
     * @param object $object
     *
     * @return array|object
     */
    public function castReverse(object $object): array|object;

    /**
     * A mapping with the keys/property names of the array/object is generated.
     * All the values are null values.
     *
     * @param string|array|object $target
     *
     * @return array<string,null>
     */
    public function generateMapping(string|array|object $target): array;
}
