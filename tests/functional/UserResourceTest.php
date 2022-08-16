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
        $client->request('POST', '/api/users', [
            'json'=>[
                'email' => 'example1@gmail.com',
                'username' => 'example',
                'password' => 'foo'
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->logIn($client, 'example1@gmail.com', 'foo');
    }

}