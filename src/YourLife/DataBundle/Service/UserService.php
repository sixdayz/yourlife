<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use YourLife\DataBundle\Document\User;

class UserService extends BaseService
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectManager */
    protected $entityManager;

    /** @var ObjectRepository */
    protected $userRepository;

    /** @var EncoderFactory */
    protected $encoderFactory;

    public function __construct(ManagerRegistry $mr, EncoderFactory $ef)
    {
        $this->managerRegistry  = $mr;
        $this->entityManager    = $mr->getManager('yourlife');
        $this->userRepository   = $mr->getRepository('YourLifeDataBundle:User');
        $this->encoderFactory   = $ef;
    }

    /**
     * @param $username
     * @param $password
     * @param $email
     * @return User
     */
    public function create($username, $password, $email = null)
    {
        $user               = new User();
        $encoder            = $this->encoderFactory->getEncoder($user);
        $encodedPassword    = $encoder->encodePassword($password, $user->getSalt());

        $user
            ->setUsername($username)
            ->setPassword($encodedPassword)
            ->setEmail($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function findByCredentials($username, $password)
    {

    }
} 