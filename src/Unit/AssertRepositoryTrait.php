<?php

namespace LekoTeam\Tests\Unit;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Трейт для тестирования репозиториев.
 *
 * @package Tests\Unit
 */
trait AssertRepositoryTrait
{
    /**
     * Мок запросов.
     *
     * @return MockObject
     */
    protected function mockQuery(): MockObject
    {
        return $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find', 'get'])
            ->getMock();
    }

    /**
     * Мок сущности.
     *
     * @param string $modelName Имя класса модели
     * @param array $attributes Атрибуты
     * @return Model
     */
    protected function mockEntity(
        string $modelName,
        array $attributes
    ): Model
    {
        $model = new $modelName();
        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * Мок модели.
     *
     * @param string $model Имя класса модели
     * @param array $params Параметры мока
     * @return Model
     */
    protected function mockModel(
        string $model,
        array $params = []
    ): Model
    {
        return $this->createConfiguredMock($model, $params);
    }

    /**
     * Проверка получения всех записей.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     */
    public function assertAll(
        string $modelName,
        string $repositoryName,
    ): void
    {
        $query = $this->mockQuery();
        $query->expects(self::once())
            ->method('get')
            ->willReturn(new Collection());

        $modelName = $this->mockModel(
            $modelName,
            [
                'newQuery' => $query,
            ]
        );

        $repositoryName = new $repositoryName($modelName);

        $result = $repositoryName->all();

        self::assertEquals(new Collection(), $result);
    }

    /**
     * Проверка получения записи по id.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     * @param array $attributes Атрибуты модели
     */
    public function assertGet(
        string $modelName,
        string $repositoryName,
        array $attributes
    ): void
    {
        $entity = $this->mockEntity(
            $modelName,
            $attributes
        );

        $query = $this->mockQuery();
        $query->expects(self::once())
            ->method('find')
            ->with($attributes['id'])
            ->willReturn($entity);

        $model = $this->mockModel(
            $modelName,
            [
                'newQuery' => $query,
            ]
        );

        $repository = new $repositoryName($model);

        /** @var Model $result */
        $result = $repository->get($attributes['id']);

        foreach ($attributes as $key => $value) {
            self::assertEquals($value, $result->$key);
        }
    }

    /**
     * Проверка успешного сохранения записи.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     */
    public function assertSaveSuccess(
        string $modelName,
        string $repositoryName,
    ): void
    {
        $model = $this->mockModel(
            $modelName,
            [
                'save' => true,
            ]
        );

        $repository = new $repositoryName($model);

        try {
            $repository->save($model);
            $this->assertTrue(true);
        } catch (Exception $e) {
            self::fail('Save throw exception: ' . $e->getMessage());
        }
    }

    /**
     * Проверка неудачного сохранения записи.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     * @param array $attributes Атрибуты модели
     */
    public function assertSaveFailed(
        string $modelName,
        string $repositoryName,
        array $attributes
    ): void
    {
        $this->expectException(Exception::class);

        $model = $this->mockModel(
            $modelName,
            [
                'save' => false,
            ]
        );

        $entity = $this->mockEntity(
            $modelName,
            $attributes
        );

        $repository = new $repositoryName($model);

        $repository->save($entity);
    }

    /**
     * Проверка успешного удаления модели.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     */
    public function assertDeleteSuccess(
        string $modelName,
        string $repositoryName,
    ): void
    {
        $model = $this->mockModel(
            $modelName,
            [
                'delete' => true,
            ]
        );

        $repository = new $repositoryName($model);

        try {
            $repository->delete($model);
            $this->assertTrue(true);
        } catch (Exception $e) {
            self::fail('Delete throw exception: ' . $e->getMessage());
        }
    }

    /**
     * Проверка неудачного удаления записи.
     *
     * @param string $modelName Имя класса модели
     * @param string $repositoryName Имя класса репозитория
     * @param array $attributes Атрибуты модели
     */
    public function assertDeleteFailed(
        string $modelName,
        string $repositoryName,
        array $attributes
    ): void
    {
        $this->expectException(Exception::class);

        $model = $this->mockModel(
            $modelName,
            [
                'delete' => false,
            ]
        );

        $entity = $this->mockEntity(
            $modelName,
            $attributes
        );

        $repository = new $repositoryName($model);

        $repository->delete($entity);
    }
}
