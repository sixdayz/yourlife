<?php

namespace YourLife\DataBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Photo
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $origin;

    /**
     * @MongoDB\String
     */
    protected $medium;

    /**
     * @MongoDB\String
     */
    protected $small;

    public function getId()
    {
        return $this->id;
    }

    public function setMedium($medium)
    {
        $this->medium = $medium;
        return $this;
    }

    public function getMedium()
    {
        return $this->medium;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setSmall($small)
    {
        $this->small = $small;
        return $this;
    }

    public function getSmall()
    {
        return $this->small;
    }
} 