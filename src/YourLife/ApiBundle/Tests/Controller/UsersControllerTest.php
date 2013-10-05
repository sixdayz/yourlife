<?php

namespace YourLife\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testCreateUser()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/v1/users',
            array(
                'username' => 'test',
                'password' => 'test'
            ));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testGetUser()
    {
        $client = $this->createClient();
        $client->request(
            'GET',
            '/api/v1/users',
            array(
                'username' => 'test',
                'password' => 'test'
            ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
