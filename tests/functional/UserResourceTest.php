<?php

namespace App\Tests\functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;
    public function testCreateUser()
    {
        $client = self::createClient();
        $client->request('POST','/api/users',[
            'json' => [
                'email' => 'cheese@gmail.com',
                'username' => 'cheeseweez',
                'password' => ''
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'cheese@gmail.com', '');
    }

}