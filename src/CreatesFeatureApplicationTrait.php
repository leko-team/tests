<?php

namespace LekoTeam\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

/**
 * Трейт создания приложения для feature тестов.
 *
 * @package Tests
 */
trait CreatesFeatureApplicationTrait
{
    use BootAppPathTrait;
    /**
     * Создаёт приложение.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require self::getAppPath();;

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
