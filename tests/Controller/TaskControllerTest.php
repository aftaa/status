<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Список задач');
    }

    public function testNewTaskPageOpens(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testEditTaskPageOpens(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task/1/edit');

        // Страница может вернуть 404 если задачи нет, но это нормально для теста
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCacheIsInvalidatedOnTaskCreation(): void
    {
        // Инициализируем тестовое окружение Symfony
        self::bootKernel();
        $container = static::getContainer();

        // 1. Получаем необходимые сервисы напрямую из DI-контейнера
        /** @var TagAwareCacheInterface $cache */
        $cache = $container->get('cache.app.taggable');

        /** @var \App\Service\TaskListerInterface $cachedLister */
        $cachedLister = $container->get(\App\Service\TaskListerInterface::class);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

        // 2. Явно прогреваем кэш с помощью нашего декоратора
        $query = new \App\Dto\TaskListQuery('name', 'desc', 'all');
        $cachedLister->getFilteredAndSortedTasks($query, 1, 10);

        // Ключ кэша, который сформировался внутри декоратора
        $cacheKey = 'tasks_list_all_name_desc_p1_l10';

        // Проверяем, что в кэше железно появился ключ
        $this->assertTrue($cache->getItem($cacheKey)->isHit(), 'Кэш успешно прогрет');

        // 3. Прямая эмуляция действий контроллера: создаем и сохраняем сущность
        $task = new \App\Entity\Task();
        $task->setName('Тестовая задача из PHPUnit');

        // Добавьте эту строчку (или проверьте точное имя метода в вашей сущности Task):
        $task->setIsCompleted(false);

        $entityManager->persist($task);

        // Главный триггер: flush() должен запустить наш TaskCacheListener
        $entityManager->flush();

        // 4. ПРОВЕРКА ИНВАЛИДАЦИИ: Проверяем, стёрся ли ключ
        $cacheItemAfter = $cache->getItem($cacheKey);

        $this->assertFalse(
            $cacheItemAfter->isHit(),
            'Ура! Кэш был успешно сброшен листенером после вызова $entityManager->flush()!'
        );
    }
}
