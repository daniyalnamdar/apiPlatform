<?php


namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;


class CustomApiTestCase extends ApiTestCase
{


    protected function createUser(string $email, string $password): User
    {

        $user = new User();
        $user->setEmail($email);
        $user->setUsername(substr($email, 0, strpos($email, '@')));


        $factory = new PasswordHasherFactory(['auto' => ['algorithm' => 'bcrypt'], 'memory-hard' => ['algorithm' => 'sodium'],]);
        $passwordHasher = $factory->getPasswordHasher('auto');
        $hashedPassword = $passwordHasher->hash($password);
        $user->setPassword($hashedPassword);

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password)
    {
        $user = $this->createUser($email, $password);
        $this->logIn($client, $email, $password);

        return $user;

    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}