# Tests

## Описание
Пакет с тестами и вспомогательными классами для Laravel-приложений.
Позволяет унифицировать тестирование и ускорить написание автотестов в проектах.

## Установка
Установите пакет через Composer (только для разработки):
```
composer require --dev leko-team/tests
```

## Конфигурация
Убедитесь, что в phpunit.xml настроен путь к базовому TestCase, если он используется из пакета:
``` 
<phpunit bootstrap="vendor/autoload.php">
    ...
</phpunit>
```

Если пакет содержит BaseTestCase, его можно использовать следующим образом:
```
use LekoTeam\Tests\BaseTestCase;

class ExampleTest extends BaseTestCase
{
// Ваши тесты
}
```

## Использование
- Используйте готовые TestCase, трейты и хелперы, предоставляемые пакетом.

- Подключайте фабрики, мок-объекты или фикстуры, если они входят в состав пакета.

- Настраивайте тестовую среду с помощью вспомогательных методов.

Пример:
```
use LekoTeam\Tests\Traits\RefreshTestDatabase;

class UserTest extends \LekoTeam\Tests\BaseTestCase
{
    use RefreshTestDatabase;

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/home');
    }
}
```

## Лицензия
Этот пакет распространяется под лицензией MIT.
Подробнее см. в файле [LICENSE](LICENSE.md).