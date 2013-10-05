<?php

namespace YourLife\DataBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class MissionCloseConditions
{
    /**
     * @MongoDB\String
     */
    protected $text;

    /**
     * @MongoDB\Boolean
     */
    protected $is_need_photos;

    /**
     * @MongoDB\Boolean
     */
    protected $is_need_comment;

    public function setIsNeedComment($is_need_comment)
    {
        $this->is_need_comment = $is_need_comment;
        return $this;
    }

    public function getIsNeedComment()
    {
        return $this->is_need_comment;
    }

    public function setIsNeedPhotos($is_need_photos)
    {
        $this->is_need_photos = $is_need_photos;
        return $this;
    }

    public function getIsNeedPhotos()
    {
        return $this->is_need_photos;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }
} 