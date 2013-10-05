<?php

namespace YourLife\DataBundle\Document;

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
     */
    protected $user_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Mission", simple=true)
     */
    protected $mission_id;

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
} 