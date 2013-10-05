<?php

namespace YourLife\DataBundle\Document;

/**
 * @MongoDB\Document(collection="mission_results")
 */
class MissionResult
{
    protected $id;

    protected $user_id;

    protected $mission_id;

    protected $mission_title;

    protected $points;

    protected $comment;

    protected $photos;
} 