<?php

namespace YourLife\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Helper\ApiExceptionType;
use YourLife\ApiBundle\Service\UserService as ApiUserService;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionCloseConditions;
use YourLife\DataBundle\Document\MissionPhoto;


class MissionsController extends Controller
{
    /** @var ApiUserService $api_service */
    protected $api_service;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);

        /** @var ApiUserService $service */
        $this->api_service = $this->get('your_life.api.user_service');
    }

    /**
     * Текущие миссии пользователя
     * */
    public function getListAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->get('token');

        $this->api_service->checkToken($token);
        $user = $this->api_service->getUser($user_id);

        // todo: получить текущие миссии пользователя

        $repo = $this->getDoctrine()->getRepository('YourLifeDataBundle:Mission');
        $result = $this->convertMissionsToArray($repo->findAll());

        return new JsonResponse($result, 200);
    }

    /**
     * Получение миссии $mission_id выполняемой пользователем $user_id
     * */
    public function getAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $mission_id = $request->get('mission_id');
        $token = $request->get('token');

        $this->api_service->checkToken($token);
        $user = $this->api_service->getUser($user_id);

        // todo: получаем миссию

        $repo = $this->getDoctrine()->getRepository('YourLifeDataBundle:Mission');
        $result = $this->convertMissionsToArray($repo->findAll());

        return new JsonResponse($result, 200);
    }

    public function updateAction() {
        return new JsonResponse();
    }

    public function createReportAction() {
        return new JsonResponse();
    }

    /**
     * Получаем доступные миссии для пользователя $user_id
     * */
    public function getAvailableMissionsAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->get('token');

        $this->api_service->checkToken($token);
        $user = $this->api_service->getUser($user_id);

        $repo = $this->getDoctrine()->getRepository('YourLifeDataBundle:Mission');

//        $missions = $repo->findBy([
//            'user_level' => $user->getLevel()
//        ]);

        $result = $this->convertMissionsToArray($repo->findAll());
        return new JsonResponse($result, 200);
    }

    private function convertMissionsToArray($missions) {
        $result = [];

        /** @var Mission $mission */
        foreach($missions as $mission) {
            $resultPhotoList = [];
            $photoList = $mission->getPhotos();
            /** @var MissionPhoto $photo */
            foreach($photoList as $photo) {
                $resultPhotoList[] = [
                    'id' => $photo->getId(),
                    'small' => $photo->getSmall(),
                    'medium' => $photo->getMedium(),
                    'origin' => $photo->getOrigin()
                ];
            }

            /** @var MissionCloseConditions $closeConditions */
            $closeConditions = $mission->getCloseConditions();

            $result[] = [
                'id' => $mission->getId(),
                'title' => $mission->getTitle(),
                'description' => $mission->getDescription(),
                'photos' => $resultPhotoList,
                'points' => $mission->getPoints(),
                'execution_time' => $mission->getExecutionTime(),
                'user_level' => $mission->getUserLevel(),
                'close_conditions' => [
                    'isNeedComment' => $closeConditions->getIsNeedComment(),
                    'isNeedPhotos' => $closeConditions->getIsNeedPhotos(),
                    'text' => $closeConditions->getText()
                ]
            ];

        }

        return $result;
    }
}
