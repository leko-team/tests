<?php

namespace LekoTeam\Tests;

/**
 * Мокирование произвольных классов произвольных сервисов.
 *
 * На вход передаётся название класса для мока (не интерфейса) и массив методов с возвращаемыми значениями.
 *
 * @package Tests
 */
trait MockServiceTrait
{
    /**
     * Мок сервисного класса с возвращаемыми значениями методов.
     *
     * @param string $className Название класса для мока
     * @param array $methods Key — название метода, value — возвращаемое значение
     * @param array $neverMethods Названия методов, которые никогда не должны вызываться
     * @param array $onceMethods Названия методов, которые должны вызываться единожды
     *
     * @return mixed
     */
    protected function mockService(string $className, array $methods = [], $neverMethods = [], $onceMethods = [])
    {
        $methodsNames = array_unique(array_merge(array_keys($methods), $neverMethods, $onceMethods));

        $stub = $this->createPartialMock($className, $methodsNames);

        foreach ($methods as $method => $result) {
            $stub->expects(in_array($method, $onceMethods) ? $this->once() : $this->any())->method($method)->willReturn($result);
        }

        foreach ($neverMethods as $method) {
            $stub->expects($this->never())->method($method);
        }

        return $stub;
    }
}
