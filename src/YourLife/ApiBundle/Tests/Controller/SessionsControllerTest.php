<?php

namespace YourLife\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SessionsControllerTest extends WebTestCase
{
    public function testCreateToken()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/v1/sessions',
            array(
                'username' => 'test',
                'password' => 'test'
            ));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
