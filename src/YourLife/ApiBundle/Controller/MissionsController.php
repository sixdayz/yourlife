<?php

namespace YourLife\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YourLife\ApiBundle\Exception\AccessErrorApiException;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Exception\MissionNotFoundApiException;
use YourLife\ApiBundle\Helper\ApiExceptionType;
use YourLife\ApiBundle\Service\UserService as ApiUserService;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionCloseConditions;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Document\Photo;
use YourLife\DataBundle\Service\MissionResultService;


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
        $token = $request->headers->get('session_token');

        $this->api_service->getUserByToken($token);
        $user = $this->api_service->getUserById($user_id);

//        todo: получить текущие миссии пользователя

        $repo = $this->get('doctrine_mongodb')->getRepository('YourLifeDataBundle:Mission');
        $result = $this->convertMissionsToArray($repo->findAll());

        return new JsonResponse($result, 200);
    }

    /**
     * Получаем доступные миссии для пользователя $user_id
     * */
    public function getAvailableMissionsAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('session_token');;

        $userByToken = $this->api_service->getUserByToken($token);
        $userById = $this->api_service->getUserById($user_id);

        if($userById != $userByToken)
            throw new AccessErrorApiException();

        $repo = $this->get('doctrine_mongodb')->getRepository('YourLifeDataBundle:Mission');

//        $missions = $repo->findBy([
//            'user_level' => $user->getLevel()
//        ]);

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
        $token = $request->headers->get('session_token');;

        $this->api_service->getUserByToken($token);
        $user = $this->api_service->getUserById($user_id);

        // todo: получаем миссию

        $repo = $this->get('doctrine_mongodb')->getRepository('YourLifeDataBundle:Mission');
        $result = $this->convertMissionsToArray($repo->findAll());

        return new JsonResponse($result, 200);
    }

    public function createResultAction() {

        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('session_token');;

        $userByToken = $this->api_service->getUserByToken($token);
        $userById = $this->api_service->getUserById($user_id);

        if($userByToken != $userById)
            throw new AccessErrorApiException();

        $mission_id = $request->get('mission_id');
        $repo = $this->get('doctrine_mongodb')->getRepository('YourLifeDataBundle:Mission');
        $mission = $repo->findOneBy([
            'id' => $mission_id,
            'user_level' => $userById->getId()
        ]);

        if($mission == null)
            throw new MissionNotFoundApiException();

        $mission_title = $request->get('mission_title');
        $points = $request->get('points');
        $comment = $request->get('comment');
        $status = $request->get('status');

        $mission = new MissionResult();

        $mission->setUser($userByToken);
        $mission->setMission($mission);
        $mission->setMissionTitle($mission_title);
        $mission->setPoints($points);
        $mission->setComment($comment);
        $mission->setStatus($status);

        /** @var MissionResultService $service */
        $service = $this->get('your_life.data.mission_result_service');
        try {
            $service->create($mission);
            return new JsonResponse(null, 201);
        } catch(\Exception $ex) {
            throw new ApiException(500, ApiExceptionType::ERROR_MISSION_RESULT_CREATE, $ex->getMessage());
        }
    }

    public function addPhotoAction() {
        return new JsonResponse();
    }

    public function updateResultAction() {
        return new JsonResponse();
    }

    private function convertMissionsToArray($missions) {
        $result = [];

        $path = $this->container->getParameter('yourlife.api.upload_photo_fullpath');

        /** @var Mission $mission */
        foreach($missions as $mission) {
            $resultPhotoList = [];
            $photoList = $mission->getPhotos();
            /** @var Photo $photo */
            foreach($photoList as $photo) {
                $resultPhotoList[] = [
                    'id' => $photo->getId(),
                    'small' => sprintf('%s/%s', $path, $photo->getSmall()),
                    'medium' => sprintf('%s/%s', $path, $photo->getMedium()),
                    'origin' => sprintf('%s/%s', $path, $photo->getOrigin())
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