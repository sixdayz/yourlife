<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionPhoto;

class MissionService extends BaseService
{
    /** @var ObjectRepository */
    protected $missionRepository;

    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct($mr);
        $this->missionRepository = $mr->getRepository('YourLifeDataBundle:Mission');
    }

    public function create(Mission $mission)
    {
        $this->documentManager->persist($mission);
        $this->documentManager->flush();
    }

    public function addPhoto(Mission $mission, $photoPath, $isNeedFlush = true)
    {

    }

    public function removePhoto(Mission $mission, MissionPhoto $photo, $isNeedFlush = true)
    {

    }

    public function remove(Mission $mission)
    {
        foreach ($mission->getPhotos() as $photo) {
            $this->removePhoto($mission, $photo, false);
        }

        $this->documentManager->remove($mission);
        $this->documentManager->flush();
    }
} 