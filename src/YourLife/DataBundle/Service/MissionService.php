<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionPhoto;
use Intervention\Image\Image;

class MissionService extends BaseService
{
    /** @var ObjectRepository */
    protected $missionRepository;

    protected $photosPath;

    public function __construct(ManagerRegistry $mr, $photosPath)
    {
        parent::__construct($mr);
        $this->missionRepository    = $this->documentManager->getRepository('YourLifeDataBundle:Mission');
        $this->photosPath           = $photosPath;
    }

    public function create(Mission $mission)
    {
        $this->documentManager->persist($mission);
        $this->documentManager->flush();
    }

    public function addPhoto(Mission $mission, $photoPath, $isNeedFlush = true)
    {
        if ( ! file_exists($this->photosPath)) {
            mkdir($this->photosPath, 0775, true);
        }

        $missionPhoto = new MissionPhoto();
        $mission->addPhoto($missionPhoto);

        // Каталог для всех копий данной фотки
        $relativePath   = substr(md5(uniqid(null, true)), 0, 3);
        $resultPath     = sprintf('%s/%s', $this->photosPath, $relativePath);

        if ( ! file_exists($resultPath)) {
            mkdir($resultPath, 0775, true);
        }

        // Пути для фоток

        $photoHash  = md5(file_get_contents($photoPath));

        $missionPhoto->setOrigin(sprintf('%s/%s_origin.jpg', $relativePath, $photoHash));
        $missionPhoto->setMedium(sprintf('%s/%s_medium.jpg', $relativePath, $photoHash));
        $missionPhoto->setSmall(sprintf('%s/%s_small.jpg', $relativePath, $photoHash));

        $origin     = sprintf('%s/%s', $this->photosPath, $missionPhoto->getOrigin());
        $medium     = sprintf('%s/%s', $this->photosPath, $missionPhoto->getMedium());
        $small      = sprintf('%s/%s', $this->photosPath, $missionPhoto->getSmall());

        // Сохраним все три фотки

        Image::make($photoPath)->save($origin);
        Image::make($photoPath)->resize(600, null, true)->save($medium);
        Image::make($photoPath)->resize(100, null, true)->save($small);

        // Сохранение миссии

        $this->documentManager->persist($mission);
        if ($isNeedFlush) {
            $this->documentManager->flush();
        }
    }

    public function removePhoto(Mission $mission, MissionPhoto $photo, $isNeedFlush = true)
    {
        $photosDir = dirname(sprintf('%s/%s', $this->photosPath, $photo->getOrigin()));

        @unlink(sprintf('%s/%s', $this->photosPath, $photo->getOrigin()));
        @unlink(sprintf('%s/%s', $this->photosPath, $photo->getMedium()));
        @unlink(sprintf('%s/%s', $this->photosPath, $photo->getSmall()));

        if (2 == count(scandir($photosDir))) {
            // Если в каталоге для фоток нет более фоток - удалим каталог
            // а то чего мусорить
            @rmdir($photosDir);
        }

        $mission->removePhoto($photo);
        $this->documentManager->persist($mission);

        if ($isNeedFlush) {
            $this->documentManager->flush();
        }
    }

    /**
     * Удаляет миссию, а вместе с ней и все фотки,
     * включая файлы из файловой системы
     * @param Mission $mission
     */
    public function remove(Mission $mission)
    {
        foreach ($mission->getPhotos() as $photo) {
            $this->removePhoto($mission, $photo, false);
        }

        $this->documentManager->remove($mission);
        $this->documentManager->flush();
    }
} 