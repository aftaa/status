<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();

        // 1. Создаём пользователя
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword(
            static::getContainer()->get('security.password_hasher')->hashPassword($user, 'password')
        );
        $user->setDisplayName('Test User');

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        // 2. Пытаемся залогиниться
        $client->request('POST', '/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertResponseRedirects('/task');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Список задач');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong',
        ]);

        $this->assertResponseRedirects('/login');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}
