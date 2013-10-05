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
        $this->userRepository   = $this->documentManager->getRepository('YourLifeDataBundle:User');
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
        $this->documentManager->flush($user);

        return $user;
    }

    public function createSessionToken(User $user)
    {
        $sessionToken = sha1(uniqid(null, true));

        $user->setSessionToken($sessionToken);
        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return $sessionToken;
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

    /**
     * Добавление баллов пользователю
     * @param User $user
     * @param $points
     * @return $this
     */
    public function appendPoints(User $user, $points)
    {
        $user->setPoints($user->getPoints() + intval($points));
        $user->setRating($user->getRating() + intval($points));
        return $this;
    }
} 