<?php

namespace YourLife\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class MissionsControllerTest extends WebTestCase
{
    public function testCreateSessionToken()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/v1/sessions',
            array(
                'username' => 'test',
                'password' => 'test'
            ));

        $arr = json_decode($client->getResponse()->getContent(), true);

        return $arr;
    }

    /**
     * @depends testCreateSessionToken
     */
    public function testGetMissions($params)
    {
        $client = $this->createClient();
        $client->request(
            'GET',
            '/api/v1/users/'.$params['user_id'].'/missions',
            array(
                'username' => 'test',
                'password' => 'test'
            ),
            array(),
            array('HTTP_SESSION_TOKEN' => $params['token'])
        );

        print_r($client->getResponse()->getContent());
    }
}
