<?php

declare(strict_types=1);

namespace App\Utilities;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use \Exception;
use \ReflectionException;
use \ReflectionParameter;
use \ReflectionUnionType;

/**
 * A very simple dependency injection container.
 */
class Container implements ContainerInterface
{
    private array $factories = [];

    /**
     * @throws Exception
     */
    public function get(string $id)
    {
        if (!isset($this->factories[$id])) {
            return $this->resolve($id);
        }

        // Call the factory function to create and return the service instance.
        $factory = $this->factories[$id];
        return $factory($this);
    }

    /**
     * @throws Exception
     */
    private function resolve(string $id)
    {
        // 1. Inspect the class we are trying to build
        try {
            $reflection_class = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new Exception("Class '{$id}' not found.");
        }

        if (!$reflection_class->isInstantiable()) {
            throw new Exception("Class '{$id}' is not instantiable.");
        }

        // 2. Inspect the constructor of the class
        $constructor = $reflection_class->getConstructor();

        if (!$constructor) {
            return new $id(); // No constructor, just create a new instance
        }

        // 3. Inspect the constructor's parameters (dependencies)
        $parameters = $constructor->getParameters();

        if (!$parameters) {
            return new $id(); // No parameters, just create a new instance
        }

        // 4. Resolve each dependency recursively
        $dependencies = array_map(
            function (ReflectionParameter $param) use ($id) {
                $name = $param->getName();
                $type = $param->getType();

                if (!$type) {
                    throw new Exception("Cannot resolve constructor parameter '{$name}' in class '{$id}'. It has no type hint.");
                }

                if ($type instanceof ReflectionUnionType || $type->isBuiltin()) {
                    throw new Exception("Cannot resolve constructor parameter '{$name}' in class '{$id}'. It is a built-in or union type.");
                }

                return $this->get($type->getName());
            },
            $parameters
        );

        // 5. Create a new instance with the resolved dependencies
        return $reflection_class->newInstanceArgs($dependencies);
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    /**
     * Sets a factory function for a service.
     * The factory will be called to create the service when it's requested.
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }
}