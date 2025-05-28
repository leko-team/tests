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
    /**
     * Создаёт приложение.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
