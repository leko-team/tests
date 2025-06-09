<?php

namespace LekoTeam\Tests;

use RuntimeException;

/**
 * Трейт для определения абсолютного пути к файлу bootstrap/app.php.
 *
 * @package Tests
 */
trait BootAppPathTrait
{
    /**
     * Возвращает абсолютный путь к файлу bootstrap/app.php
     *
     * @return string Полный путь
     *
     * @throws RuntimeException
     */
    protected static function getAppPath(): string
    {
        $path = dirname(__DIR__, 4) . '/bootstrap/app.php';

        if (!file_exists($path)) {
            throw new RuntimeException("Laravel bootstrap file not found at: $path");
        }

        return $path;
    }
}

