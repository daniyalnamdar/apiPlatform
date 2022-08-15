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

        $client->request('POST', '/api/cheeses');
        $this->assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client,'test@gmail.com', '123');

        $client->request('POST', '/api/cheeses',[
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1@example.com', 'foo');
        $user2 = $this->createUser('user2@example.com', 'foo');

        $cheeseListing = new CheeseListing();
        $cheeseListing->setOwner($user1);
        $cheeseListing->setTitle('ss');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('yummmy');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client, 'user2@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
           'json' => ['title' => 'updated']
        ]);
        $this->assertResponseStatusCodeSame(403);

    }


}