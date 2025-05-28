<?php

namespace LekoTeam\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Абстрактный класс для unit тестов.
 *
 * @package Tests
 */
abstract class AbstractUnitTestCase extends BaseTestCase
{
    use CreatesApplicationTrait,
        MockModelTrait;
}
