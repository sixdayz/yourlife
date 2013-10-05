<?php

namespace YourLife\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\DataBundle\Service\UserService;


class UsersController extends Controller
{
    public function getAction()
    {
        $request = $this->getRequest();

        $username = $request->get('username');
        $password = $request->get('password');

        /** @var UserService $service */
        $service = $this->get('your_life.data.user_service');

        try
        {
            /** @var YourLife/DataBundle/Document/User $user */
            $user = $service->findByCredentials($username, $password);

            if($user == null)
                return new JsonResponse(null, 404);

            return new JsonResponse(
                array(
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername(),
                    'roles' => $user->getRoles()
                ), 200);
        }
        catch(\Exception $ex)
        {
            throw new ApiException(500, 'your_life_api_error_user_create', $ex->getMessage());
        }
    }

    public function createAction()
    {
        $request = $this->getRequest();

        $username = $request->get('username');
        $password = $request->get('password');

        /** @var UserService $service */
        $service = $this->get('your_life.data.user_service');

        /** @var YourLife/DataBundle/Document/User $user */
        $user = $service->findByCredentials($username, $password);

        if($user != null)
            throw new ApiException(400, 'your_life_api_user_exists', 'Пользователь с данным логином уже существует!');

        try
        {
            $service->create($username, $password);
            return new JsonResponse(null, 201);
        }
        catch(\Exception $ex)
        {
            throw new ApiException(500, 'your_life_api_error_user_create', $ex->getMessage());
        }
    }
}
