<?php

namespace LekoTeam\Tests;

/**
 * Мокирование фасадов.
 *
 * @package Tests
 */
trait MockFacadeTrait
{
    use MockServiceTrait;

    /**
     * Мокирование класса фасада.
     *
     * @param string $facadeClass Название класса фасада
     * @param string $serviceClass Название класса-сервиса, который
     * @param array $methods
     * @param array $neverMethods
     * @param array $onceMethods
     */
    protected function mockFacade(string $facadeClass, string $serviceClass, array $methods = [], $neverMethods = [], $onceMethods = [])
    {
        $service = $this->mockService($serviceClass, $methods, $neverMethods, $onceMethods);

        $facadeClass::swap($service);
    }
}
