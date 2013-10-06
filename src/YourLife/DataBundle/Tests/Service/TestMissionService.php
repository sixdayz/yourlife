<?php

namespace YourLife\DataBundle\Tests\Service;

use Doctrine\Common\Persistence\ObjectManager;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionCloseConditions;
use YourLife\DataBundle\Service\MissionService;
use YourLife\DataBundle\Tests\ContainerAwareUnitTestCase;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class TestMissionService extends ContainerAwareUnitTestCase
{
    /** @var MissionService */
    protected $missionService;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectManager */
    protected $documentManager;

    /** @var Mission */
    protected $mission;

    protected $missionPhoto;

    protected $photosPath;

    protected function setUp()
    {
        $this->missionService       = $this->get('your_life.data.mission_service');
        $this->managerRegistry      = $this->get('doctrine_mongodb');
        $this->documentManager      = $this->managerRegistry->getManager('yourlife');
        $this->photosPath           = $this->getParameter('yourlife.data.mission_photos_path');
        $this->missionPhoto         = $this->getParameter('kernel.root_dir') .
            '/../src/YourLife/DataBundle/TestData/Service/Mission/mission_photo.jpg';

        // Создадим миссию для тестов

        $this->mission = new Mission();
        $this->mission->setTitle('Тестовая миссия');

        $closeConditions = new MissionCloseConditions();
        $closeConditions->setIsNeedComment(true);
        $closeConditions->setIsNeedPhotos(true);
        $closeConditions->setText('Свари утюг');

        $this->mission->setCloseConditions($closeConditions);

        $this->documentManager->persist($this->mission);
        $this->documentManager->flush();
    }

    public function testAddPhoto()
    {
        $this->missionService->addPhoto($this->mission, $this->missionPhoto);
        $missionPhotos = $this->mission->getPhotos();

        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $missionPhotos[0]->getOrigin())));
        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $missionPhotos[0]->getMedium())));
        $this->assertTrue(file_exists(sprintf('%s/%s', $this->photosPath, $missionPhotos[0]->getSmall())));
    }

    protected function tearDown()
    {
        $this->missionService->remove($this->mission);
    }
} 