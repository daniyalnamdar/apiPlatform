<?php

namespace App\Tests\functional;


use App\Entity\CheeseListing;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;


class CheeseListingResourceTest extends CustomApiTestCase
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

//        $this->createUser('cheeseplease@example.com', '$2y$13$Da7nL4GBqavL3hwT5q5u4uOquDrZzTwgDCIT43TXdU3hwH3.I5HKW');
//        $this->logIn($client, 'cheeseplease@example.com', 'foo');
        $this->createUserAndLogIn($client, 'cheeseplease@example.com', 'foo');

//        $client->request('POST', '/api/cheeses');
//        $this->assertResponseStatusCodeSame(400);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1@example.com', '$2y$13$Da7nL4GBqavL3hwT5q5u4uOquDrZzTwgDCIT43TXdU3hwH3.I5HKW');
        $user2 = $this->createUser('user2@example.com', '$2y$13$Da7nL4GBqavL3hwT5q5u4uOquDrZzTwgDCIT43TXdU3hwH3.I5HKW');
        $cheeseListing = new CheeseListing();
        $cheeseListing->setTitle('Block of cheddar');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('mmmm');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client, 'user2@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => ['title' => 'updated']
        ]);

        $this->assertResponseStatusCodeSame(403, 'only author can updated');

        $this->logIn($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => ['title' => 'updated']
        ]);
        $this->assertResponseStatusCodeSame(200);
    }


}