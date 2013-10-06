<?php

namespace YourLife\DataBundle\Tests\Service;

use Doctrine\Common\Persistence\ObjectManager;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Document\User;
use YourLife\DataBundle\Enum\MissionResultStatus;
use YourLife\DataBundle\Service\MissionResultService;
use YourLife\DataBundle\Tests\ContainerAwareUnitTestCase;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class TestMissionResultService extends ContainerAwareUnitTestCase
{
    /** @var MissionResultService */
    protected $resultService;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectManager */
    protected $documentManager;

    /** @var MissionResult */
    protected $missionResult;

    protected $resultPhoto;

    protected $photosPath;

    /** @var Mission */
    protected $mission;

    /** @var User */
    protected $user;

    protected function setUp()
    {
        $this->resultService        = $this->get('your_life.data.mission_result_service');
        $this->managerRegistry      = $this->get('doctrine_mongodb');
        $this->documentManager      = $this->managerRegistry->getManager('yourlife');
        $this->photosPath           = $this->getParameter('yourlife.data.mission_result_photos_path');
        $this->resultPhoto          = $this->getParameter('kernel.root_dir') .
            '/../src/YourLife/DataBundle/TestData/Service/Mission/mission_photo.jpg';

        // Создадим миссию для тестов

        $this->missionResult = new MissionResult();
        $this->missionResult->setMissionTitle('Тестовая миссия');
        $this->missionResult->setPoints(12);
        $this->missionResult->setStatus(MissionResultStatus::IN_PROGRESS);
        $this->missionResult->setComment('Описалово окончания');

        $this->mission = new Mission();
        $this->mission->setTitle('Тестовая миссия');
        $this->missionResult->setMission($this->mission);

        $this->user = new User();
        $this->user->setUsername(sprintf('test_user_%s', uniqid()));
        $this->missionResult->setUser($this->user);

        $this->documentManager->persist($this->missionResult);
        $this->documentManager->persist($this->mission);
        $this->documentManager->persist($this->user);

        $this->documentManager->flush();
    }

    public function testAddPhoto()
    {
        $this->resultService->addPhoto($this->missionResult, $this->resultPhoto);
        $photos = $this->missionResult->getPhotos();

        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $photos[0]->getOrigin())));
        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $photos[0]->getMedium())));
        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $photos[0]->getSmall())));
    }

    protected function tearDown()
    {
        $this->resultService->remove($this->missionResult);

        $this->documentManager->remove($this->mission);
        $this->documentManager->remove($this->user);
        $this->documentManager->flush();
    }
} 