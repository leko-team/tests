<?php

namespace LekoTeam\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

/**
 * Трейт создания приложения.
 *
 * @package Tests
 */
trait CreatesApplicationTrait
{
    use MockDatabaseConnectionTrait;

    /**
     * Создаёт приложение.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require $this->getAppPath();
        $app->make(Kernel::class)->bootstrap();

        $this->setConnectionToApplication($app);

        return $app;
    }

    /**
     * Возвращает абсолютный путь к файлу bootstrap/app.php
     *
     * @return string Полный путь
     *
     * @throws RuntimeException
     */
    protected function getAppPath(): string
    {
        $path = dirname(__DIR__, 4) . '/bootstrap/app.php';

        if (!file_exists($path)) {
            throw new RuntimeException("Не удалось найти bootstrap/app.php по пути: $path");
        }

        return $path;
    }
}
