<?php

namespace App\Tests\functional;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;


class CheeseListingResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;
    public function testCreateCheeseListing()
    {
        $client = self::createClient();
//        $client->request('POST', '/api/cheeses', [
//            'headers' => ['Content-Type' => 'application/json']
//        ]);
        $client->request('POST', '/api/cheeses');
        $this->assertResponseStatusCodeSame(401);

        $user = new User();
        $user->setEmail('cheeseplease@example.com');
        $user->setUsername('cheeseplease');
        $user->setPassword('$2y$13$Da7nL4GBqavL3hwT5q5u4uOquDrZzTwgDCIT43TXdU3hwH3.I5HKW')
        ;
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'cheeseplease@example.com',
                'password' => 'foo'
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }
}