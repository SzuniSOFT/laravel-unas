<?php


namespace SzuniSoft\Unas\Tests;


use function dd;
use Orchestra\Testbench\TestCase;
use ReflectionClass;

abstract class BaseTestCase extends TestCase
{

    /**
     * @param        $object
     * @param string $propertyName
     * @param        $value
     *
     * @return \SzuniSoft\Unas\Tests\BaseTestCase
     * @throws \ReflectionException
     */
    protected function mockProperty($object, string $propertyName, $value)
    {
        $reflectionClass = new ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);

        return $this;
    }

}
