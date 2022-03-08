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
                'password' => '$2y$13$1jtwBNxC2/UEfprTms36/OvFM0MFoh.CZ8i0vcYUCqmnyYAH/OT9a',
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

       $this->logIn($client, 'cheese@gmail.com', 'tada');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'cheese@gmail.com', 'tada');
        $client->request('PUT', '/api/users/'.$user->getId(), [
            'json' => [
                'username' => 'newusername'
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'newusername'
        ]);
    }
}