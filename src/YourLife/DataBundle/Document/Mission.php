<?php

namespace YourLife\DataBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="missions")
 */
class Mission
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $title;

    /**
     * @MongoDB\String
     */
    protected $description;

    /**
     * @MongoDB\EmbedMany(targetDocument="MissionPhoto")
     * @var MissionPhoto[]
     */
    protected $photos;

    /**
     * @MongoDB\Int
     */
    protected $points;

    /**
     * @MongoDB\Int
     */
    protected $user_level;

    /**
     * @MongoDB\Int
     */
    protected $execution_time;

    /**
     * @MongoDB\EmbedOne(targetDocument="MissionCloseConditions")
     * @var MissionCloseConditions
     */
    protected $close_conditions;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    public function addPhoto(MissionPhoto $photo)
    {
        $this->photos[] = $photo;
        return $this;
    }

    public function removePhoto(MissionPhoto $photo)
    {
        $this->photos->removeElement($photo);
        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }

    public function getUserLevel()
    {
        return $this->user_level;
    }

    public function setUserLevel($userLevel)
    {
        $this->user_level = $userLevel;
        return $this;
    }

    public function getExecutionTime()
    {
        return $this->execution_time;
    }

    public function setExecutionTime($executionTime)
    {
        $this->execution_time = $executionTime;
        return $this;
    }

    public function getCloseConditions()
    {
        return $this->close_conditions;
    }

    public function setCloseConditions(MissionCloseConditions $closeConditions)
    {
        $this->close_conditions = $closeConditions;
        return $this;
    }
} 