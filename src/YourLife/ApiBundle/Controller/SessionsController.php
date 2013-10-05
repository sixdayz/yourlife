<?php

namespace YourLife\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Helper\ApiExceptionType;
use YourLife\DataBundle\Service\UserService;


class SessionsController extends Controller
{
    public function createAction()
    {
        $request = $this->getRequest();

        $username = $request->get('username');
        $password = $request->get('password');

        /** @var UserService $service */
        $service = $this->get('your_life.data.user_service');

        /** @var YourLife/DataBundle/Document/User $user */
        $user = $service->findByCredentials($username, $password);

        if($user == null)
            throw new ApiException(404, ApiExceptionType::USER_NOT_FOUND, sprintf('Пользователь с логином %s не существует!', $username));

        try
        {
            $token = $service->createSessionToken($user);
            return new JsonResponse(array(
                'user_id' => $user->getId(),
                'token' => $token
            ), 201);
        }
        catch(\Exception $ex)
        {
            throw new ApiException(500, ApiExceptionType::ERROR_TOKEN_CREATE, $ex->getMessage());
        }
    }
}
