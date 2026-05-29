<?php
//
//namespace App\Tests\Functional;
//
//use App\Entity\Task;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//
//class TaskControllerFunctionalTest extends WebTestCase
//{
//    private EntityManagerInterface $entityManager;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//        // Не bootKernel() — он вызывается автоматически в createClient()
//        $container = static::getContainer();
//        $this->entityManager = $container->get('doctrine')->getManager();
//        $this->clearTasks();
//        $this->seedTasks();
//    }
//
//    private function clearTasks(): void
//    {
//        $this->entityManager
//            ->createQuery('DELETE FROM App\\Entity\\Task t')
//            ->execute();
//    }
//
//    private function seedTasks(): void
//    {
//        $tasks = [
//            ['A задача', false],
//            ['B задача', true],
//            ['C задача', false],
//            ['D задача', true],
//        ];
//
//        foreach ($tasks as [$name, $completed]) {
//            $task = new Task();
//            $task->setName($name);
//            $task->setIsCompleted($completed);
//            $task->setCreatedAt(new \DateTimeImmutable());
//            $this->entityManager->persist($task);
//        }
//        $this->entityManager->flush();
//    }
//
//    protected function tearDown(): void
//    {
//        parent::tearDown();
//        $this->entityManager->close();
//        unset($this->entityManager);
//    }
//
//    private function createAuthenticatedClient()
//    {
//        return static::createClient([], [
//            'HTTP_HOST' => 'localhost:8003',
//        ]);
//    }
//
//    public function testFilterByCompleted(): void
//    {
//        $client = $this->createAuthenticatedClient();
//        $client->followRedirects();
//        $client->request('GET', '/task?filter=completed');
//        $this->assertResponseIsSuccessful();
//    }
//
//    public function testFilterByNotCompleted(): void
//    {
//        $client = $this->createAuthenticatedClient();
//        $client->followRedirects();
//        $client->request('GET', '/task?filter=not_completed');
//        $this->assertResponseIsSuccessful();
//    }
//
//    public function testSortByNameAsc(): void
//    {
//        $client = $this->createAuthenticatedClient();
//        $client->followRedirects();
//        $client->request('GET', '/task?sort=name&order=asc');
//        $this->assertResponseIsSuccessful();
//    }
//}
