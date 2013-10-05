<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Document\Photo;

class MissionResultService extends BaseService
{
    /** @var ObjectRepository */
    protected $missionResultRepository;

    protected $photosPath;

    /** @var UserService */
    protected $userService;

    public function __construct(ManagerRegistry $mr, UserService $userService, $photosPath)
    {
        parent::__construct($mr);
        $this->missionRepository    = $this->documentManager->getRepository('YourLifeDataBundle:MissionResult');
        $this->photosPath           = $photosPath;
        $this->userService          = $userService;
    }

    public function create(MissionResult $missionResult)
    {

    }

    public function addPhoto(MissionResult $missionResult, $photoPath, $isNeedFlush = true)
    {

    }

    public function removePhoto(MissionResult $missionResult, Photo $photo, $isNeedFlush = true)
    {

    }

    /**
     * Удаляет результат, а вместе с ним и все фотки,
     * включая файлы из файловой системы
     * @param MissionResult $missionResult
     */
    public function remove(MissionResult $missionResult)
    {

    }
} 