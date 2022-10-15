<?php

namespace Core\Container;

use Closure;
use http\Exception\RuntimeException;
use ReflectionClass;
use ReflectionException;

class Container
{
    /**
     * The container's instances.
     *
     * @var array the key is the FQ class name and the value is the binding
     */
    protected array $instances = [];


    /**
     * Get an instance of the given class by its FQN.
     *
     * @param string $class
     * @return mixed
     * @throws ReflectionException
     */
    public function getInstance(string $class): mixed
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        return $this->resolve($class);
    }


    /**
     * Register a class.
     *
     * @param Object $value The already initialized class
     * @return void
     */
    public function register(Object $value): void
    {
        $reflector = new ReflectionClass($value);
        $class = $reflector->getName();

        if (isset($this->instances[$class])) {
            throw new RuntimeException('Instance already registered');
        }

        $this->instances[$class] = $value;
    }

    /**
     * Resolve the given class and its dependencies.
     *
     * @param $class
     * @return mixed
     * @throws ReflectionException
     */
    private function resolve($class): mixed
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new RuntimeException('Class not found');
        }

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException('Class not instantiable');
        }

        // Get the constructor of the class
        $constructor = $reflection->getConstructor();

        // If there is no constructor there are no dependencies, so we can just create a new instance of the class
        if (is_null($constructor)) {
            return new $class;
        }

        // Get the parameters of the constructor
        $parameters = $constructor->getParameters();

        // Resolve the parameters and its dependencies recursively
        $dependencies = $this->getDependencies($parameters);

        // Create a new instance of the class, using the resolved dependencies
        $instance = $reflection->newInstanceArgs($dependencies);

        // Register the instance
        $this->instances[$class] = $instance;

        return $instance;
    }

    /**
     * Get the resolved dependencies of the given parameters.
     *
     * @param array $parameters
     * @return array
     * @throws ReflectionException
     */
    private function getDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType()->getName() ?? null;

            if (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->resolve($dependency);
            }
        }

        return $dependencies;
    }

    /**
     * Try to resolve a non-class parameter.
     *
     * @param $parameter
     * @return mixed
     */
    private function resolveNonClass($parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new RuntimeException('Cannot resolve non class parameter');
    }

}