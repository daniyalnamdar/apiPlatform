<?php

namespace App\Tests\functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends ApiTestCase
{
    protected $preserveGlobalState = FALSE;
    protected $runTestInSeparateProcess = TRUE;

    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();

        $client->request('POST', '/api/cheeses');
        $this->assertResponseStatusCodeSame(401);

        $user = new User();
        $user->setEmail('test1@gmail.com');
        $user->setUsername('test1');
        // pass is 123
        $user->setPassword('$2y$13$GWLyL0MVuoJSBomtusQpP.MhebEB3to4zlvjoNDvNQUBCpCTPvrCe');



        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $client->request('POST', '/login', [
            'json' => [
                'email' => 'test1@gmail.com',
                'password' => '123',
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);

    }


}