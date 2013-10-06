<?php

namespace YourLife\ApiBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use YourLife\DataBundle\Document\User;
use YourLife\ApiBundle\Exception\InvalidTokenApiException;
use YourLife\ApiBundle\Exception\UserNotFoundApiException;

class UserService extends BaseService
{
    /** @var ObjectRepository */
    protected $userRepository;

    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct($mr);
        $this->userRepository = $mr->getRepository('YourLifeDataBundle:User');
    }

    public function getUserByToken($token)
    {
        $currentUser = $this->userRepository->findOneBy(array('session_token' => $token));

        if($currentUser == null)
            throw new InvalidTokenApiException();

        return $currentUser;
    }

    public function getUserById($user_id)
    {
        $user = $this->userRepository->find($user_id);

        if($user == null)
            throw new UserNotFoundApiException();

        return $user;
    }
}