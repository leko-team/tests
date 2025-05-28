<?php

namespace LekoTeam\Tests;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;

/**
 * Абстрактный класс для feature тестов.
 *
 * @package Tests
 */
abstract class AbstractFeatureTestCase extends BaseTestCase
{
    use CreatesFeatureApplicationTrait;

    /**
     * Настраивает окружение теста.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->disableMiddleware();
    }

    /**
     * Отключает миддлвары которые ограничивают тестирование.
     */
    protected function disableMiddleware(): void
    {
        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            ThrottleRequests::class,
        ]);
    }
}
