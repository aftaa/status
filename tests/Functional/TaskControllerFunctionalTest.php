<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerFunctionalTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Functional tests require refactoring.');
    }

    public function testFilterByCompleted(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task?filter=completed');
        $this->assertResponseRedirects('/login');
    }

    public function testFilterByNotCompleted(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task?filter=not_completed');
        $this->assertResponseRedirects('/login');
    }

    public function testSortByNameAsc(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8003',
        ]);
        $client->request('GET', '/task?sort=name&order=asc');
        $this->assertResponseRedirects('/login');
    }
}
