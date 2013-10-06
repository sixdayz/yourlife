<?php

namespace YourLife\DataBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="mission_results")
 */
class MissionResult
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", simple=true)
     * @var User
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Mission", simple=true)
     * @var Mission
     */
    protected $mission;

    /**
     * @MongoDB\String
     */
    protected $mission_title;

    /**
     * @MongoDB\Int
     */
    protected $points;

    /**
     * @MongoDB\String
     */
    protected $comment;

    /**
     * @MongoDB\EmbedMany(targetDocument="Photo")
     * @var Photo[]
     */
    protected $photos;

    /**
     * @MongoDB\String
     */
    protected $status;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setMission(Mission $mission)
    {
        $this->mission = $mission;
        return $this;
    }

    public function getMission()
    {
        return $this->mission;
    }

    public function setMissionTitle($mission_title)
    {
        $this->mission_title = $mission_title;
        return $this;
    }

    public function getMissionTitle()
    {
        return $this->mission_title;
    }

    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function addPhoto(Photo $photo)
    {
        $this->photos[] = $photo;
        return $this;
    }

    public function removePhoto(Photo $photo)
    {
        $this->photos->removeElement($photo);
        return $this;
    }

    public function getPhotos()
    {
        return $this->photos;
    }
} 