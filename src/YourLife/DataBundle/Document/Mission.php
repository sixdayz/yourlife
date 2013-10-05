<?php

namespace YourLife\DataBundle\Document;

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

    public function getId()
    {
        return $this->id;
    }
} 