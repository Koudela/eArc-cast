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

use ReflectionClass;

class CastService implements CastServiceInterface
{
    /** @var <object|array>[][] */
    protected array $references = [];
    /** @var <string|null>[][] */
    protected array $mappings = [];

    public function cast(array|object $origin, string|object $target, ?array &$mapping = null): object
    {
        $target = $this->castSimple($origin, $target, $mapping);

        $this->references[spl_object_id($target)][] = $origin;
        $this->mappings[spl_object_id($target)][] = $mapping;

        return $target;
    }

    public function castReverse(object $object): array|object
    {
        if (!array_key_exists(spl_object_id($object), $this->references) || empty($this->references[spl_object_id($object)])) {
            return $object;
        }

        $target = array_pop($this->references[spl_object_id($object)]);
        $mapping = array_pop($this->mappings[spl_object_id($object)]);
        $mapping = $this->reverseMapping($mapping);

        return $this->castSimple($object, $target, $mapping);
    }

    public function castSimple(array|object $origin, string|array|object $target, ?array &$mapping = null): array|object
    {
        if (is_null($mapping)) {
            $mapping = $this->generateMapping($target);
        }

        $values = $this->getValues($origin, $mapping);

        if (is_array($target)) {
            return $this->castToArray($values, $target);
        }

        return $this->castToObject($values, $target);
    }

    protected function generateMapping(string|array|object $target): array
    {
        $mapping = [];

        if (is_array($target)) {
            foreach ($target as $key => $value) {
                $mapping[$key] = null;
            }

            return $mapping;
        }

        $objReflection = new ReflectionClass($target);

        do {
            foreach ($objReflection->getProperties() as $property) {
                $mapping[$property->getName()] = null;
            }
        } while ($objReflection = $objReflection->getParentClass());

        return $mapping;
    }

    protected function reverseMapping(array $mapping): array
    {
        $reversedMapping = [];

        foreach ($mapping as $key => $value) {
            $reversedMapping[is_null($value) ? $key : $value] = is_null($value) ? null : $key;
        }

        return $reversedMapping;
    }

    protected function getValues(array|object $origin, array $mapping): array
    {
        $values = [];

        if (is_array($origin)) {
            foreach ($mapping as $key => $value) {
                if (array_key_exists($key, $origin)) {
                    $values[is_null($value) ? $key : $value] = $origin[$key];
                }
            }

            return $values;
        }

        $originReflection = new ReflectionClass($origin);

        do {
            $this->addValuesFromReflection($originReflection, $values, $origin, $mapping);
        } while ($originReflection = $originReflection->getParentClass());

        return $values;
    }

    protected function addValuesFromReflection(ReflectionClass $originReflection, array &$values, object $origin, array $mapping)
    {
        foreach ($originReflection->getProperties() as $property) {
            if (array_key_exists($property->getName(), $mapping)) {
                $property->setAccessible(true);
                $key = is_null($mapping[$property->getName()]) ? $property->getName() : $mapping[$property->getName()];
                if (!array_key_exists($key, $values)) {
                    $values[$key] = $property->getValue($origin);
                }
            }
        }
    }

    protected function castToArray(array $values, array $target): array
    {
        foreach ($values as $key => $value)
        {
            $target[$key] = $value;
        }

        return $target;
    }

    protected function castToObject(array $values, string|object $target): object
    {
        $targetReflection = new ReflectionClass($target);

        if (is_string($target)) {
            $target = $targetReflection->newInstanceWithoutConstructor();
        }

        do {
            $this->addValuesToReflection($targetReflection, $target, $values);
        } while ($targetReflection = $targetReflection->getParentClass());

        return $target;
    }

    protected function addValuesToReflection(ReflectionClass $targetReflection, object $target, array $values)
    {
        $properties = $targetReflection->getProperties();

        foreach ($properties as $property) {
            if (array_key_exists($property->getName(), $values)) {
                $property->setAccessible(true);
                $property->setValue($target, $values[$property->getName()]);
            }
        }
    }
}
