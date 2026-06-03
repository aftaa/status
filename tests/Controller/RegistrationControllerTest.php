<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testRegistrationWithValidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('Register', [
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->assertResponseRedirects('/task/');
    }
}
