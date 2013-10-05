<?php

namespace YourLife\DataBundle\Service;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use YourLife\DataBundle\Document\User;

class UserService extends BaseService
{
    /** @var ObjectRepository */
    protected $userRepository;

    /** @var EncoderFactory */
    protected $encoderFactory;

    public function __construct(ManagerRegistry $mr, EncoderFactory $ef)
    {
        parent::__construct($mr);
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

        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return $user;
    }

    public function findByCredentials($username, $password)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy([ 'username' => $username ]);
        if ( ! $user) {
            return null;
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        if ( ! $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            return null;
        }

        return $user;
    }

    public function remove(User $user)
    {
        $this->documentManager->remove($user);
        $this->documentManager->flush();
    }
} 