<?php

namespace LekoTeam\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

/**
 * Трейт создания приложения.
 *
 * @package Tests
 */
trait CreatesApplicationTrait
{
    use MockDatabaseConnectionTrait, BootAppPathTrait;

    /**
     * Создаёт приложение.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require self::getAppPath();
        $app->make(Kernel::class)->bootstrap();

        $this->setConnectionToApplication($app);

        return $app;
    }
}
