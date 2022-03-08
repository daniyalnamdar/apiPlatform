<?php

namespace App\ApiPlatform\Test;

use App\ApiPlatform\Test\ApiTestCase;
use App\ApiPlatform\Test\Client;
use App\Entity\User;


class CustomApiTestCase extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{


    protected function createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername(substr($email, 0, strpos($email, '@')));

//        $encoded = self::$container->get('security.password_encoder')->encodePassword($user, $password);
        $user->setPassword($password);

        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(\ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => 'tada'
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(\ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client $client, string $email, string $password): User
    {
        $user = $this->createUser($email, $password);

        $this->logIn($client, $email, 'tada');

        return $user;
    }
}
