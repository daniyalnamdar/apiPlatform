<?php

namespace App\Tests\functional;

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


}