<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Intervention\Image\Image;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Document\Photo;

class MissionResultService extends BaseService
{
    protected $photosPath;

    public function __construct(ManagerRegistry $mr, $photosPath)
    {
        parent::__construct($mr);
        $this->photosPath = $photosPath;
    }

    public function create(MissionResult $missionResult)
    {
        $this->documentManager->persist($missionResult);
        $this->documentManager->flush();
    }

    public function addPhoto(MissionResult $missionResult, $photoPath, $isNeedFlush = true)
    {
        if ( ! file_exists($this->photosPath)) {
            mkdir($this->photosPath, 0775, true);
        }

        $photo = new Photo();
        $missionResult->addPhoto($photo);

        // Каталог для всех копий данной фотки
        $relativePath   = substr(md5(uniqid(null, true)), 0, 3);
        $resultPath     = sprintf('%s/%s', $this->photosPath, $relativePath);

        if ( ! file_exists($resultPath)) {
            mkdir($resultPath, 0775, true);
        }

        // Пути для фоток

        $photoHash  = md5(file_get_contents($photoPath));

        $photo->setOrigin(sprintf('%s/%s-o.jpg', $relativePath, $photoHash));
        $photo->setMedium(sprintf('%s/%s-m.jpg', $relativePath, $photoHash));
        $photo->setSmall(sprintf('%s/%s-s.jpg', $relativePath, $photoHash));

        $origin     = sprintf('%s/%s', $this->photosPath, $photo->getOrigin());
        $medium     = sprintf('%s/%s', $this->photosPath, $photo->getMedium());
        $small      = sprintf('%s/%s', $this->photosPath, $photo->getSmall());

        // Сохраним все три фотки

        Image::make($photoPath)->save($origin);
        Image::make($photoPath)->resize(600, null, true)->save($medium);
        Image::make($photoPath)->resize(100, null, true)->save($small);

        // Сохранение миссии

        $this->documentManager->persist($missionResult);
        if ($isNeedFlush) {
            $this->documentManager->flush();
        }
    }

    public function removePhoto(MissionResult $missionResult, Photo $photo, $isNeedFlush = true)
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

        $missionResult->removePhoto($photo);
        $this->documentManager->persist($missionResult);

        if ($isNeedFlush) {
            $this->documentManager->flush();
        }
    }

    /**
     * Удаляет результат, а вместе с ним и все фотки,
     * включая файлы из файловой системы
     * @param MissionResult $missionResult
     */
    public function remove(MissionResult $missionResult)
    {
        foreach ($missionResult->getPhotos() as $photo) {
            $this->removePhoto($missionResult, $photo, false);
        }

        $this->documentManager->remove($missionResult);
        $this->documentManager->flush();
    }
} 