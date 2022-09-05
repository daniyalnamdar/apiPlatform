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
        $client->request('POST', '/api/cheeses', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);

        $authenticatedUser = $this->createUserAndLogIn($client, 'cheeseplease@example.com', 'foo');
        $otherUser = $this->createUser('otheruser@example.com', 'foo');
        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000
        ];
        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData,
        ]);
        $this->assertResponseStatusCodeSame(201, 'missing owner');

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => '/api/users/'.$otherUser->getId()],
        ]);
        $this->assertResponseStatusCodeSame(422, 'not passing the correct owner');

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => '/api/users/'.$authenticatedUser->getId()],
        ]);

        $this->assertResponseStatusCodeSame(201);
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
           'json' => ['title' => 'updated', 'owner' => '/api/users/'. $user2->getId()]
        ]);
        $this->assertResponseStatusCodeSame(403);

    }
    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $user = $this->createUser('cheeseplese@example.com', 'foo');

        $cheeseListing1 = new CheeseListing('cheese1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setDescription('cheese');
        $cheeseListing2 = new CheeseListing('cheese2');
        $cheeseListing2->setOwner($user);
        $cheeseListing2->setPrice(1000);
        $cheeseListing2->setIsPublished(true);
        $cheeseListing2->setDescription('cheese');
        $cheeseListing3 = new CheeseListing('cheese3');
        $cheeseListing3->setOwner($user);
        $cheeseListing3->setPrice(1000);
        $cheeseListing3->setIsPublished(true);
        $cheeseListing3->setDescription('cheese');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->persist($cheeseListing2);
        $em->persist($cheeseListing3);
        $em->flush();

        $client->request('GET', '/api/cheeses');

        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'cheeseplese@example.com', 'foo');
        $cheeseListing1 = new CheeseListing('cheese1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setDescription('cheese');
        $cheeseListing1->setIsPublished(false);
        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->flush();
        $client->request('GET', '/api/cheeses/'.$cheeseListing1->getId());
        $this->assertResponseStatusCodeSame(404);
        $client->request('GET', '/api/users/'.$user->getId());
        $data = $client->getResponse()->toArray();
        $this->assertEmpty($data['cheeseListings']);
    }


}