<?php

namespace LekoTeam\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as BaseQuery;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Мокирование класса модели: запросы, сохранение модели и т.д.
 *
 * @package Tests
 */
trait MockModelTrait
{
    use MockDatabaseConnectionTrait;

    /**
     * Возвращает мок модели для выборки запросов из таблицы.
     *
     * Возможно использование:
     *
     * ```php
     * $model->scopes(['scope1', 'scope2'])->get(); // множественный выбор
     * $model->where('id', $id)->first(); // один результат
     * $model->whereNot('column1', 2)->count(); // количество найденных записей
     * ```
     *
     * Четвёртым параметром необходимо использовать массив вида:
     * ```php
     * [
     *  'first' => new Model(),
     *  'get' => new Collection(),
     *  'exists' => false,
     *  'count' => 10
     * ]
     * ```
     *
     * Т.е. массив методов, которые будут вызываться для выборки с их результатами обработки.
     *
     * @param string $modelClass Название класса модели для мокирования
     * @param string|null $expectedSql Ожидаемый SQL-запрос
     * @param array $bindings Ожидаемые биндинги в SQL-запросе
     * @param array $results Результат выборки (ключ массива - название метода: count, exists, get, first, etcz, значение - результат выборки)
     *
     * @return Model
     * @throws Exception
     */
    protected function mockModelSelect(
        string $modelClass,
        ?string $expectedSql,
        array $bindings,
        array $results
    ): Model
    {
        $connection = $this->mockConnection();

        $baseBuilder = new BaseQuery($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());

        $methods = array_unique(array_merge(array_keys($results), ['get', 'first', 'count', 'exists']));

        $builder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([
                $baseBuilder
            ])
            ->onlyMethods($methods)
            ->getMock();

        $returnSelectResult = function ($result) use ($builder, $expectedSql, $bindings) {
            return function () use ($result, $expectedSql, $bindings, $builder) {
                if (!empty($expectedSql)) {
                    $this->assertEquals($expectedSql, $builder->toSql());
                    $this->assertEquals($bindings, $builder->getBindings());
                }
                return $result;
            };
        };

        foreach ($methods as $method) {
            if (!array_key_exists($method, $results)) {
                $builder->expects($this->never())->method($method);
            } else {
                $builder->expects($this->any())->method($method)
                    ->willReturnCallback($returnSelectResult($results[$method]));
            }
        }

        /** @var Builder $builder */

        $model = $this->createPartialMock($modelClass, [
            'newQuery', 'save',
        ]);

        $model->method('newQuery')->willReturn($builder);

        /** @var Model $model */
        $builder->setModel($model);

        return $model;
    }

    /**
     * Возвращаеь мок модели для проведения основных операций CRUD:
     *
     * - save(),
     * - remove();
     * - etc.
     *
     * @param string $modelClass Название класса для мокирования
     * @param array $operations Выполняемые операции: key - название метода, value - результат выполнения метода
     *
     * @return Model
     * @throws Exception
     */
    protected function mockModelCRUD(string $modelClass, array $operations): Model
    {
        $methods = array_unique(array_merge(array_keys($operations), ['save', 'remove']));

        $model = $this->createPartialMock($modelClass, $methods);

        foreach ($methods as $method) {
            if (!method_exists($model, $method)) {
                throw new \RuntimeException("Method $method doesnt exists at $modelClass");
            }

            if (!array_key_exists($method, $operations)) {
                $model->expects($this->never())->method($method);
            } else {
                $model->expects($this->any())->method($method)
                    ->willReturn($operations[$method]);
            }
        }

        /** @var Model $model */
        return $model;
    }

    /**
     * Возвравщает мок связи в модели:
     *
     * - HasMany;
     * - HasOne;
     * - BelongsTo;
     * - etc.
     *
     * Например, может мокировать такой вызов:
     *
     * ```php
     * $newItem = $model->items()->create([
     *     'field1' => 'value1'
     * ]);
     * ```
     *
     * Пример мока:
     * ```php
     * $relation = $this->>mockRelationCRUD(Item::class, HasMany::class, [
     *  'create' => [
     *      'field1' => 'value1'
     *  ]
     * ]);
     *
     * $model = $this->mockModelCRUD(Order::class, [
     *      'items' => $relation
     * ]);
     * ```
     *
     * @param string $modelClass Название класса зависимой модели
     * @param string $relationClass Название класса для зависимости (например, HasMany)
     * @param array $operations Мокируемые операции зависимости
     *
     * @return Relation
     * @throws Exception
     */
    protected function mockModelRelationCRUD(string $modelClass, string $relationClass, array $operations): Relation
    {
        $methods = array_unique(array_merge(array_keys($operations), ['create']));

        $relation = $this->createPartialMock($relationClass, $methods);

        foreach ($methods as $method) {
            if (!method_exists($relation, $method)) {
                throw new \RuntimeException("Method $method doesnt exists at $relationClass");
            }

            if (!array_key_exists($method, $operations)) {
                $relation->expects($this->never())->method($method);
            } elseif ($method === 'create') {
                // проверить вызов create
                $relation->expects($this->any())
                    ->method($method)
                    ->willReturnCallback(function (array $fields) use ($modelClass, $method, $operations) {
                        $model = new $modelClass;
                        $expectedFields = $operations[$method];
                        foreach ($expectedFields as $field => $value) {
                            $this->assertArrayHasKey($field, $fields);
                            $this->assertEquals($value, $fields[$field]);
                            $model->$field = $fields[$field];
                        }
                        return $model;
                    });
            } else {
                $relation->expects($this->any())->method($method)->willReturn($operations[$method]);
            }
        }

        return $relation;
    }
}
