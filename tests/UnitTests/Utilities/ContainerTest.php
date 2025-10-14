<?php

declare(strict_types=1);

namespace Tests\UnitTests\Utilities;

use App\Utilities\Container;
use PHPUnit\Framework\TestCase;
use Exception;
use Closure;

// --- Test Fixtures ---

class NoConstructorDependency
{
    public NoConstructorDependency $singleton;
}

class ServiceDependency
{
}

class ConcreteService
{
    public function __construct(public ServiceDependency $dependency)
    {
    }
}

interface UnresolvableInterface
{
}

class ContainerTest extends TestCase
{
    public function testItCanSetAndGetExplicitlyDefinedService(): void
    {
        $container = new Container();
        $service = new \stdClass();

        $container->set('my_service', function () use ($service) {
            return $service;
        });

        $this->assertTrue($container->has('my_service'));
        $this->assertSame($service, $container->get('my_service'));
    }

    public function testItCanAutowireClassWithNoConstructor(): void
    {
        $container = new Container();
        $instance = $container->get(NoConstructorDependency::class);

        $this->assertInstanceOf(NoConstructorDependency::class, $instance);
    }

    public function testItCanAutowireClassWithDependenciesRecursively(): void
    {
        $container = new Container();
        $service = $container->get(ConcreteService::class);

        $this->assertInstanceOf(ConcreteService::class, $service);
        $this->assertInstanceOf(ServiceDependency::class, $service->dependency);
    }

    public function testItThrowsExceptionForNonExistentClass(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Class 'Tests\UnitTests\Utilities\NonExistentClass' not found.");

        $container = new Container();
        $container->get('Tests\UnitTests\Utilities\NonExistentClass');
    }

    public function testItThrowsExceptionForNonInstantiableClass(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Class 'Tests\UnitTests\Utilities\UnresolvableInterface' is not instantiable.");

        $container = new Container();
        $container->get(UnresolvableInterface::class);
    }

    public function testHasReturnsFalseForUnresolvableClass(): void
    {
        $container = new Container();
        $this->assertFalse($container->has('NonExistentClass'));
    }

    // public function testGetReturnsSameInstanceForRegisteredServices(): void
    // {
    //     $container = new Container();

    //     // Register as a "singleton"
    //     $container->set(NoConstructorDependency::class, function ($c) {
    //         if (!isset($c->singleton)) {
    //             $c->singleton = new NoConstructorDependency();
    //         }

    //         return $c->singleton;
    //     });

    //     $instance1 = $container->get(NoConstructorDependency::class);
    //     $instance2 = $container->get(NoConstructorDependency::class);

    //     $this->assertSame($instance1, $instance2);
    // }
}