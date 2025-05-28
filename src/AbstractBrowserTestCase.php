<?php

namespace LekoTeam\Tests;

use App\Models\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\ElementClickInterceptedException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;

/**
 * Абстрактный класс для browser тестов.
 */
abstract class AbstractBrowserTestCase extends BaseTestCase
{
// TODO Закмоентированно в задачи task #30659 Обновить "framework".
//  Вынес в отдельную задачу task #31136 Рефакторинг тестов
//    /**
//     * Подготовка к выполнению тестов.
//     *
//     * @beforeClass
//     * @return void
//     */
//    #[Test]
//    public static function prepare(): void
//    {
//    }

    /**
     * Настраивает окружение теста.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setBasePaths();
    }

    /**
     * Устанавливает пути.
     */
    protected function setBasePaths(): void
    {
        Browser::$storeScreenshotsAt = base_path('tests/Browser/screenshots');
        Browser::$storeConsoleLogAt = base_path('tests/Browser/console');
    }

    use CreatesApplicationTrait;

    /**
     * Создаёт экземпляр RemoteWebDriver.
     *
     * @return RemoteWebDriver
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
            '--ignore-ssl-errors',
            '--ignore-certificate-errors',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            'http://selenium:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /**
     * Отключение режима HEADLESS браузера.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Авторизует пользователя.
     *
     * @param Browser $browser Браузер
     * @param User $user Пользователь
     * @return void
     */
    public function login(Browser $browser, User $user): void
    {
        $browser->visit(route('main'))
            ->clickLink('Войти')
            ->type('email', $user->email)
            ->type('password', '12345678910')
            ->pressAndWaitFor('Войти')
            ->assertSee('Последние отзывы');
    }

    /**
     * Разлогинивает пользователя.
     *
     * @param Browser $browser Браузер
     * @return void
     * @throws ElementClickInterceptedException
     * @throws NoSuchElementException
     */
    public function logout(Browser $browser): void
    {
        $browser->click('.profile-navigation__head')
            ->clickLink('Выход');
    }
}
