<?php

namespace YourLife\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            array(),
            array(),
            array('HTTP_X_SESSION_TOKEN' => $params['token'])
        );

        print_r($client->getResponse()->getContent());
        print_r("\r\n\r\n");
    }

    /**
     * @depends testCreateSessionToken
     */
    public function testGetMission($params)
    {
        $client = $this->createClient();
        $client->request(
            'GET',
            '/api/v1/users/'.$params['user_id'].'/missions/525138d9454f2a1e720041a7',
            array(),
            array(),
            array('HTTP_X_SESSION_TOKEN' => $params['token'])
        );

        print_r($client->getResponse()->getContent());
        print_r("\r\n\r\n");
    }

    /**
     * @depends testCreateSessionToken
     */
    public function testCreateMissionResult($params)
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/v1/users/'.$params['user_id'].'/missions',
            array(
                'mission_id' => '525138d9454f2a1e720041a7',
                'mission_title' => 'title',
                'status' => 'in_progress'
            ),
            array(),
            array('HTTP_X_SESSION_TOKEN' => $params['token'])
        );

        $arr = json_decode($client->getResponse()->getContent(), true);
        $params['mission_result_id'] = $arr['mission_result_id'];
        return $params;
    }

    /**
     * @depends testCreateMissionResult
     */
    public function testAddPhoto($params)
    {
        $photoPath = __DIR__ . '/../../../DataBundle/TestData/Service/Mission/mission_photo.jpg';

        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/v1/users/'.$params['user_id'].'/missions/'.$params['mission_result_id'].'/photos',
            array(),
            array(
                new UploadedFile($photoPath, '123')
            ),
            array('HTTP_X_SESSION_TOKEN' => $params['token'])
        );

        print_r($client->getResponse()->getContent());
    }
}
