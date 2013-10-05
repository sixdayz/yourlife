<?php

namespace YourLife\ApiBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use YourLife\DataBundle\Document\User;

class UserService extends BaseService
{
    /** @var ObjectRepository */
    protected $userRepository;

    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct($mr);
        $this->userRepository = $mr->getRepository('YourLifeDataBundle:User');
    }

    public function checkToken($token)
    {
        $currentUser = $this->userRepository->findOneBy(array('token' => $token));

        if($currentUser == null)
            throw new ApiException(400, ApiExceptionType::INVALID_TOKEN, 'Неверный токен');
    }

    public function getUser($user_id)
    {
        $user = $mr->find($user_id);

        if($user == null)
            throw new ApiException(404, ApiExceptionType::USER_NOT_FOUND, 'Пользователь не найден');

        return $user;
    }
}