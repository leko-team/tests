<?php

namespace LekoTeam\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PHPUnit\Framework\MockObject\MockBuilder;

/**
 * Трейт для тестирования моделей.
 *
 * @todo Переименовать параметры в соответствии с параметрами вы связях.
 *
 * @method MockBuilder getMockBuilder(string $className)
 * @method assertEquals($expected, $got, ?string $message = null)
 * @method assertInstanceOf(string $className, mixed $got, ?string $message = null)
 * @method assertIsArray(mixed $got, ?string $message = null)
 * @method assertNotEmpty($got, ?string $message = null)
 * @method assertContainsOnlyInstancesOf(string $className, array $got, ?string $message = null)
 * @method assertCount(int $cnt, array $got, ?string $message = null)
 * @method once()
 *
 * @package Tests\Unit
 */
trait AssertModelTrait
{
    /**
     * Проверка правильного указания названия таблицы.
     *
     * @param string $className Имя класса
     * @param string $tableName Имя таблицы
     * @param string|null $message Сообщение об ошибке
     */
    public function assertTableName(
        string $className,
        string $tableName,
        ?string $message = ''
    ): void
    {
        /** @var Model $model */
        $model = new $className;
        $this->assertEquals($tableName, $model->getTable(), $message);
    }

    /**
     * Проверка правильного указания названия подключения к БД.
     *
     * @param string $className Имя класса
     * @param string $connectionName Имя таблицы
     * @param string|null $message Сообщение об ошибке
     */
    public function assertConnectionName(
        string $className,
        string $connectionName,
        ?string $message = ''
    ): void
    {
        /** @var Model $model */
        $model = new $className;
        $this->assertEquals($connectionName, $model->getConnectionName(), $message);
    }

    /**
     * Проверка связи HasOne.
     *
     * @param string $className Имя класса
     * @param string $propertyName Свойство для получения зависимой модели
     * @param string $foreignClass Класс зависимой модели
     * @param string $foreignField Поле в зависимой модели
     * @param string $localField Поле в текущей модели
     * @param string|null $message Сообщение об ошибке
     */
    public function assertHasOne(
        string $className,
        string $propertyName,
        string $foreignClass,
        string $foreignField,
        string $localField,
        ?string $message = ''
    ): void
    {
        $foreignModel = new $foreignClass;

        $hasOne = $this->getMockBuilder(HasOne::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $hasOne->expects($this->once())
            ->method('getResults')
            ->willReturn($foreignModel);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['hasOne'])
            ->getMock();
        $model->expects($this->once())
            ->method('hasOne')
            ->with($foreignClass, $foreignField, $localField)
            ->willReturn($hasOne);

        /** @var Model $model */
        $this->assertEquals($foreignModel, $model->$propertyName, $message);
    }

    /**
     * Проверка связи BelongsTo.
     *
     * @param string $className
     * @param string $propertyName Свойство для получения зависимой модели
     * @param string $foreignClass Класс зависимой модели
     * @param string $foreignField Поле в текущей модели
     * @param string $ownerField Поле в зависимой модели
     * @param string|null $message
     */
    public function assertBelongsTo(
        string $className,
        string $propertyName,
        string $foreignClass,
        string $foreignField,
        string $ownerField,
        ?string $message = ''
    ): void
    {
        $foreignModel = new $foreignClass;

        $belongsTo = $this->getMockBuilder(BelongsTo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $belongsTo->expects($this->once())
            ->method('getResults')
            ->willReturn($foreignModel);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['belongsTo'])
            ->getMock();
        $model->expects($this->once())
            ->method('belongsTo')
            ->with($foreignClass, $foreignField, $ownerField)
            ->willReturn($belongsTo);

        /** @var Model $model */
        $this->assertEquals($foreignModel, $model->$propertyName, $message);
    }

    /**
     * Проверка связи BelongsToMany.
     *
     * @param string $className Имя класса модели
     * @param string $propertyName Свойство для получения зависимой модели
     * @param string $foreignClass Класс зависимой модели
     * @param string $throughTable Имя промежуточной таблицы
     * @param string $foreignField Поле в текущей модели
     * @param string $ownerField Поле в зависимой модели
     * @param string|null $message Сообщение
     */
    public function assertBelongsToMany(
        string $className,
        string $propertyName,
        string $foreignClass,
        string $throughTable,
        string $foreignField,
        string $ownerField,
        ?string $message = ''
    ): void
    {
        $foreignModel = new $foreignClass;

        $belongsToMany = $this->getMockBuilder(BelongsToMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults', 'createdAt', 'updatedAt'])
            ->getMock();
        $belongsToMany->expects($this->once())
            ->method('getResults')
            ->willReturn($foreignModel);
        $belongsToMany->method('createdAt')
            ->willReturn('created_at');
        $belongsToMany->method('updatedAt')
            ->willReturn('updated_at');

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['belongsToMany'])
            ->getMock();
        $model->expects($this->once())
            ->method('belongsToMany')
            ->with($foreignClass, $throughTable, $foreignField, $ownerField)
            ->willReturn($belongsToMany);

        /** @var Model $model */
        $this->assertEquals($foreignModel, $model->$propertyName, $message);
    }

    /**
     * Проверка связи hasManyThrough.
     *
     * @param string $className Имя класса
     * @param string $propertyName Имя свойства
     * @param string $relatedClass
     * @param string $throughClass
     * @param string $firstKey
     * @param string $secondKey
     * @param string $localKey
     * @param string $secondLocalKey
     * @param string|null $message Сообщение об ошибке
     */
    public function assertHasManyThrough(
        string $className,
        string $propertyName,
        string $relatedClass,
        string $throughClass,
        string $firstKey,
        string $secondKey,
        string $localKey,
        string $secondLocalKey,
        ?string $message = ''
    ): void
    {
        $items = [
            new $relatedClass,
            new $relatedClass,
            new $relatedClass,
        ];

        $hasMany = $this->getMockBuilder(HasManyThrough::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $hasMany->expects($this->once())
            ->method('getResults')
            ->willReturn($items);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['hasManyThrough'])
            ->getMock();
        $model->expects($this->once())
            ->method('hasManyThrough')
            ->with($relatedClass, $throughClass, $firstKey, $secondKey, $localKey, $secondLocalKey)
            ->willReturn($hasMany);

        /** @var Model $model */
        $result = $model->$propertyName;

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf($relatedClass, $result);
        $this->assertCount(count($items), $items);
    }

    /**
     * Проверка связи hasOneThrough.
     *
     * @param string $className Имя класса
     * @param string $propertyName Имя свойства
     * @param string $relatedClass
     * @param string $throughClass
     * @param string $firstKey
     * @param string $secondKey
     * @param string $localKey
     * @param string $secondLocalKey
     * @param string|null $message Сообщение об ошибке
     */
    public function assertHasOneThrough(
        string $className,
        string $propertyName,
        string $relatedClass,
        string $throughClass,
        string $firstKey,
        string $secondKey,
        string $localKey,
        string $secondLocalKey,
        ?string $message = ''
    ): void
    {
        $item = new $relatedClass;

        $hasOneThrough = $this->getMockBuilder(HasOneThrough::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $hasOneThrough->expects($this->once())
            ->method('getResults')
            ->willReturn($item);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['hasOneThrough'])
            ->getMock();
        $model->expects($this->once())
            ->method('hasOneThrough')
            ->with($relatedClass, $throughClass, $firstKey, $secondKey, $localKey, $secondLocalKey)
            ->willReturn($hasOneThrough);

        /** @var Model $model */
        $result = $model->$propertyName;

        $this->assertEquals($item, $result, $message);
    }

    /**
     * Проверка связи hasMany.
     *
     * @param string $className Имя класса
     * @param string $propertyName Имя свойства
     * @param string $relatedCLass
     * @param string $foreignKey
     * @param string $localKey
     */
    public function assertHasMany(
        string $className,
        string $propertyName,
        string $relatedCLass,
        string $foreignKey,
        string $localKey
    ): void
    {
        $items = [
            new $relatedCLass,
            new $relatedCLass,
            new $relatedCLass,
        ];

        $hasMany = $this->getMockBuilder(HasMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $hasMany->expects($this->once())
            ->method('getResults')
            ->willReturn($items);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['hasMany'])
            ->getMock();
        $model->expects($this->once())
            ->method('hasMany')
            ->with($relatedCLass, $foreignKey, $localKey)
            ->willReturn($hasMany);

        /** @var Model $model */
        $result = $model->$propertyName;

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf($relatedCLass, $result);
        $this->assertCount(count($items), $items);
    }

    /**
     * Проверка связи MorphTo.
     *
     * @param string $className Имя класса модели
     * @param string $propertyName Свойство для получения зависимой модели
     * @param string $foreignClass Класс зависимой модели
     * @param string $foreignType Поле типа в текущей модели
     * @param string $foreignField Поле в текущей модели
     * @param string $ownerField Поле в зависимой модели
     * @param string|null $message Сообщение
     */
    public function assertMorphTo(
        string $className,
        string $propertyName,
        string $foreignClass,
        string $foreignType,
        string $foreignField,
        string $ownerField,
        ?string $message = ''
    ): void
    {
        $foreignModel = new $foreignClass;

        $morphTo = $this->getMockBuilder(MorphTo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $morphTo->expects($this->once())
            ->method('getResults')
            ->willReturn($foreignModel);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['morphTo'])
            ->getMock();
        $model->expects($this->once())
            ->method('morphTo')
            ->with($propertyName, $foreignType, $foreignField, $ownerField)
            ->willReturn($morphTo);

        /** @var Model $model */
        $this->assertEquals($foreignModel, $model->$propertyName, $message);
    }

    /**
     * Проверка связи MorphMany.
     *
     * @todo Добавить проверку необязательных параметров. Тут и в остальных методах.
     * @param string $className Имя класса модели
     * @param string $propertyName Свойство для получения зависимой модели
     * @param string $relatedClass Класс зависимой модели
     * @param string $relatedName Название метода зависимой модели
     * @param string|null $relatedType Поле типа в текущей модели
     * @param string|null $relatedField Поле в текущей модели
     * @param string|null $ownerField Поле в зависимой модели
     */
    public function assertMorphMany(
        string $className,
        string $propertyName,
        string $relatedClass,
        string $relatedName,
        ?string $relatedType = null,
        ?string $relatedField = null,
        ?string $ownerField = null,
    ): void
    {
        $items = [
            new $relatedClass,
            new $relatedClass,
            new $relatedClass,
        ];

        $morphMany = $this->getMockBuilder(MorphMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResults'])
            ->getMock();
        $morphMany->expects($this->once())
            ->method('getResults')
            ->willReturn($items);

        $model = $this->getMockBuilder($className)
            ->onlyMethods(['morphMany'])
            ->getMock();
        $model->expects($this->once())
            ->method('morphMany')
            ->with($relatedClass, $relatedName)
            ->willReturn($morphMany);

        /** @var Model $model */
        $result = $model->$propertyName;

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf($relatedClass, $result);
        $this->assertCount(count($items), $items);
    }

    /**
     * Проверяет связь между моделями.
     *
     * @param Model|string $model Модель или имя класса модели
     * @param string $relationMethod Имя метода, который определяет связь
     * @param string $expectedRelationType Ожидаемый тип связи (hasOne, belongsTo, hasMany, belongsToMany)
     * @param string $expectedRelatedClass Ожидаемый класс связанной модели
     * @param array $expectedParameters Ожидаемые параметры связи
     * @param string $message Сообщение об ошибке
     */
    protected function assertModelRelation(
        Model|string $model,
        string $relationMethod,
        string $expectedRelationType,
        string $expectedRelatedClass,
        array $expectedParameters = [],
        string $message = ''
    ): void {
        if (is_string($model)) {
            $model = new $model();
        }

        $this->assertTrue(
            method_exists($model, $relationMethod),
            "Метод {$relationMethod} не существует в классе " . get_class($model)
        );

        $relation = $model->{$relationMethod}();

        $expectedRelationClass = "Illuminate\\Database\\Eloquent\\Relations\\{$expectedRelationType}";
        $this->assertInstanceOf(
            $expectedRelationClass,
            $relation,
            $message ?: "Неверный тип связи, ожидается {$expectedRelationType}"
        );

        $this->assertEquals(
            $expectedRelatedClass,
            get_class($relation->getRelated()),
            "Неверный класс связанной модели, ожидается {$expectedRelatedClass}"
        );

        if ($relation instanceof BelongsToMany && isset($expectedParameters[0])) {
            $this->assertEquals(
                $expectedParameters[0],
                $relation->getTable(),
                'Неверное имя промежуточной таблицы'
            );
        }

        if ($relation instanceof BelongsTo && isset($expectedParameters['foreign_key'])) {
            $this->assertEquals(
                $expectedParameters['foreign_key'],
                $relation->getForeignKeyName(),
                'Неверный внешний ключ для связи belongsTo'
            );
        }

        if ($relation instanceof HasMany && ($expectedParameters['ordered'] ?? false)) {
            $query = $relation->getQuery();
            $orders = $query->getQuery()->orders;

            $this->assertNotEmpty(
                $orders,
                "Отсутствует сортировка для связи {$relationMethod}"
            );

            $this->assertEquals(
                'id',
                $orders[0]['column'],
                "Неверная колонка сортировки для связи {$relationMethod}"
            );
        }

        if ($relation instanceof HasMany && ($expectedParameters['where_conditions'] ?? false)) {
            $query = $relation->getQuery();
            $whereGroups = $query->getQuery()->wheres;

            $this->assertNotEmpty(
                $whereGroups,
                "Отсутствуют условия where для связи {$relationMethod}"
            );

            $this->assertArrayHasKey(
                0,
                $whereGroups,
                "Отсутствует основная группа условий"
            );

            if (isset($whereGroups[0]['type']) && $whereGroups[0]['type'] === 'Closure') {
                if (isset($expectedParameters['reference_time'])) {
                    $referenceTime = Carbon::parse($expectedParameters['reference_time']);
                    $expectedTime = $referenceTime->copy()->subSeconds(Agent::UPDATE_PERIOD_SECONDS);

                    $subQuery = $whereGroups[0]['query'];
                    $conditions = $subQuery->wheres;

                    $this->assertCount(
                        2,
                        $conditions,
                        "Неверное количество условий where"
                    );

                    $this->assertEquals(
                        'Null',
                        $conditions[0]['type'],
                        "Первое условие должно быть whereNull"
                    );
                    $this->assertEquals(
                        'online_at',
                        $conditions[0]['column'],
                        "Неверная колонка в условии whereNull"
                    );

                    $this->assertEquals(
                        'Basic',
                        $conditions[1]['type'],
                        "Второе условие должно быть where"
                    );
                    $this->assertEquals(
                        'online_at',
                        $conditions[1]['column'],
                        "Неверная колонка во втором условии"
                    );
                    $this->assertEquals(
                        '<',
                        $conditions[1]['operator'],
                        "Неверный оператор во втором условии"
                    );

                    $conditionTime = Carbon::parse($conditions[1]['value']);
                    $this->assertTrue(
                        $conditionTime->equalTo($expectedTime),
                        "Неверное время в условии where"
                    );
                }
            }
        }
    }
}
