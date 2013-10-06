<?php

namespace YourLife\ApiBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Exception\UserNotFoundApiException;
use YourLife\ApiBundle\Enum\ApiExceptionType;
use YourLife\DataBundle\Document\User;
use YourLife\DataBundle\Service\UserLevelService;
use YourLife\DataBundle\Service\UserService;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class UsersController extends Controller
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectRepository */
    protected $userRepository;

    /** @var UserLevelService */
    protected $userLevel;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->userLevel        = $this->get('your_life.data.user_level_service');
        $this->managerRegistry  = $this->get('doctrine_mongodb');
        $this->userRepository   = $this->managerRegistry
            ->getManager('yourlife')
            ->getRepository('YourLifeDataBundle:User');
    }

    public function getListAction()
    {
        $users = $this->userRepository->findBy([], $this->getRequest()->query->get('sort', []));
        return new JsonResponse(array_map(function(User $user) {
            return [
                'id'                => $user->getId(),
                'username'          => $user->getUsername(),
                'email'             => $user->getEmail(),
                'points'            => $user->getPoints(),
                'rating'            => $user->getRating(),
                'level'             => $user->getLevel(),
                'points_percent'    => $this->userLevel->getPercentForLevelPoints($user->getPoints())
            ];
        }, $users));
    }

    public function getAction()
    {
        /** @var User $user */
        $user = $this->userRepository->find($this->getRequest()->get('id'));
        if (null !== $user) {
            throw new UserNotFoundApiException();
        }

        return new JsonResponse([
            'id'                => $user->getId(),
            'username'          => $user->getUsername(),
            'email'             => $user->getEmail(),
            'points'            => $user->getPoints(),
            'rating'            => $user->getRating(),
            'level'             => $user->getLevel(),
            'points_percent'    => $this->userLevel->getPercentForLevelPoints($user->getPoints())
        ]);
    }

    public function createAction()
    {
        $request = $this->getRequest();

        $username = $request->get('username');
        $password = $request->get('password');

        /** @var UserService $service */
        $service = $this->get('your_life.data.user_service');

        /** @var User $user */
        $user = $this->userRepository->findOneBy([ 'username' => $username ]);
        if (null !== $user) {
            throw new ApiException(400, ApiExceptionType::USER_ALREADY_EXISTS, 'Пользователь с данным логином уже существует!');
        }

        try {

            $user = $service->create($username, $password);
            return new JsonResponse([
                'id' => $user->getId()
            ], 201);

        } catch (\Exception $ex) {
            throw new ApiException(500, ApiExceptionType::ERROR_USER_CREATE, $ex->getMessage());
        }
    }
}
