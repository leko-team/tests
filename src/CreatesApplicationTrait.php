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
    use MockDatabaseConnectionTrait;

    /**
     * Создаёт приложение.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->setConnectionToApplication($app);

        return $app;
    }
}
