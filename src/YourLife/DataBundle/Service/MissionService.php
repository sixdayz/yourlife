<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class MissionService extends BaseService
{
    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct($mr);
    }

    //public function create()
} 