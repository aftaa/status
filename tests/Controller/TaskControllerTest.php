<?php

namespace App\Tests\Controller;

use App\Dto\TaskListQuery;
use App\Entity\Task;
use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\Enum\TaskStatus;
use App\Repository\UserRepository;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TaskControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task/');

        $this->assertResponseRedirects('/login');
    }

    public function testNewTaskPageRequiresLogin(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task/new');

        $this->assertResponseRedirects('/login');
    }

    public function testTasksPageWorksWhenLoggedIn(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com');

        if (!$testUser) {
            $this->markTestSkipped('No test user found. Run seeds first.');
        }

        $client->loginUser($testUser);
        $client->request('GET', '/task/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Список задач');
    }

    public function testCacheIsInvalidatedOnTaskCreation(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var TagAwareCacheInterface $cache */
        $cache = $container->get('cache.app.taggable');

        /** @var \App\Service\TaskListerInterface $cachedLister */
        $cachedLister = $container->get(\App\Service\TaskListerInterface::class);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

        $query = new TaskListQuery(
            sort: TaskSort::NAME,
            order: TaskOrder::ASC,
            filter: TaskFilter::ALL,
            page: new Page(1),
            limit: new Limit(10),
        );
        $cachedLister->getFilteredAndSortedTasks($query);

        $cacheKey = 'tasks_list_all_name_asc_p1_l10';
        $this->assertTrue($cache->getItem($cacheKey)->isHit(), 'Кэш прогрет');

        $task = new Task();
        $task->setName('Тестовая задача из PHPUnit');
        $task->setStatus(TaskStatus::NOT_COMPLETED);

        $entityManager->persist($task);
        $entityManager->flush();

        $cacheItemAfter = $cache->getItem($cacheKey);
        $this->assertFalse($cacheItemAfter->isHit(), 'Кэш сброшен листенером');
    }
}
