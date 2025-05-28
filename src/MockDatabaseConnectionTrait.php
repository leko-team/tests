<?php

namespace LekoTeam\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\Processor;
use PDO;

/**
 * Трейт для мокирования подключения к БД.
 *
 * @package Tests
 */
trait MockDatabaseConnectionTrait
{
    /**
     * Возвращает мок менеджера для работы с базами данными.
     *
     * @return DatabaseManager
     */
    protected function mockDatabaseManager(): DatabaseManager
    {
        $manager = $this->getMockBuilder(DatabaseManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['connection'])
            ->getMock();

        $manager->method('connection')->willReturn($this->mockConnection());

        /** @var DatabaseManager $manager */
        return $manager;
    }

    /**
     * Возвращает мок коннекта к базе данных.
     *
     * @param array $params
     * @return Connection
     */
    protected function mockConnection(array $params = []): Connection
    {
        $connection = $this->getMockBuilder(PostgresConnection::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'transaction',
                'getQueryGrammar',
                'insert',
                'getPostProcessor',
                'getPdo',
                'getDriverName',
            ])
            ->getMock();

        $connection->method('transaction')->willReturnCallback(
            function ($transactionCallback) {
                return $transactionCallback();
            }
        );

        $connection->method('getQueryGrammar')->willReturn(new PostgresGrammar());

        if (!empty($params['insert'])) {
            $connection->method('insert')->willReturnCallback($params['insert']);
        } else {
            $connection->method('insert')->willReturn(true);
        }

        $connection->method('getDriverName')->willReturn('pgsql');
        $connection->method('getPostProcessor')->willReturn(new Processor());

        $pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->method('getPdo')->willReturn($pdo);

        /** @var Connection $connection */
        return $connection;
    }

    /**
     * Подключает мок коннекта к базе данных к приложению.
     *
     * @param Application $app
     */
    protected function setConnectionToApplication(Application $app): void
    {
        $app->bind('db', function () {
            return $this->mockDatabaseManager();
        });
    }
}
