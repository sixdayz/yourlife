<?php

namespace YourLife\ApiBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

class BaseService
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectManager */
    protected $documentManager;

    public function __construct(ManagerRegistry $mr)
    {
        $this->managerRegistry  = $mr;
        $this->documentManager  = $mr->getManager('yourlife');
    }
} 