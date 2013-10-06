<?php

namespace YourLife\ApiBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YourLife\ApiBundle\Exception\AccessErrorApiException;
use YourLife\ApiBundle\Exception\ApiException;
use YourLife\ApiBundle\Exception\ApiExceptionDetail;
use YourLife\ApiBundle\Exception\MissionNotFoundApiException;
use YourLife\ApiBundle\Exception\PhotoUploadApiException;
use YourLife\ApiBundle\Enum\ApiExceptionType;
use YourLife\ApiBundle\Service\UserService as ApiUserService;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionCloseConditions;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Document\Photo;
use YourLife\DataBundle\Enum\MissionResultStatus;
use YourLife\DataBundle\Service\MissionResultService;


class MissionsController extends Controller
{
    /** @var ApiUserService $api_service */
    protected $api_service;

    /** @var  ObjectManager $doctrine */
    protected $doctrine;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);

        $this->api_service = $this->get('your_life.api.user_service');
        $this->doctrine = $this->get('doctrine_mongodb');
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

        $repo = $this->doctrine->getRepository('YourLifeDataBundle:MissionResult');
        $missions = $repo->findBy([
            '$or' => [
                ['status' => MissionResultStatus::IN_PROGRESS],
                ['status' => MissionResultStatus::COMPLETE]
            ],
            'user' => $user_id
        ]);

        $result = $this->convertMissionsToArray($missions);

        return new JsonResponse($result, 200);
    }

    /**
     * Получаем доступные миссии для пользователя $user_id
     * */
    public function getAvailableMissionsAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('session_token');

        $userByToken = $this->api_service->getUserByToken($token);
        $userById = $this->api_service->getUserById($user_id);

        if($userById != $userByToken) {
            throw new AccessErrorApiException();
        }

        $repo = $this->doctrine->getRepository('YourLifeDataBundle:Mission');

        $missions = $repo->findBy([
            'user_level' => $userById->getLevel()
        ]);

        $result = $this->convertMissionsToArray($missions);
        return new JsonResponse($result, 200);
    }

    /**
     * Получение миссии $mission_id выполняемой пользователем $user_id
     * */
    public function getAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('session_token');

        $userByToken = $this->api_service->getUserByToken($token);
        $userById = $this->api_service->getUserById($user_id);

//        if($userByToken != $userById) {
//
//        }

        // todo: просматривать только миссии пользователя

        $mission_id = $request->get('mission_id');
        $repo = $this->doctrine->getRepository('YourLifeDataBundle:Mission');
        $mission = $repo->find($mission_id);
        $result = $this->convertMissionsToArray($mission);

        return new JsonResponse($result, 200);
    }

    public function createResultAction() {

        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('session_token');

        $userByToken = $this->api_service->getUserByToken($token);
        $userById = $this->api_service->getUserById($user_id);

        if($userByToken != $userById) {
            throw new AccessErrorApiException();
        }

        $mission_id = $request->get('mission_id');
        $repo = $this->doctrine->getRepository('YourLifeDataBundle:Mission');
        $mission = $repo->findOneBy([
            'id' => $mission_id,
            'user_level' => $userById->getId()
        ]);

        if($mission == null) {
            throw new MissionNotFoundApiException();
        }

        $mission_title = $request->get('mission_title');
        $points = $request->get('points');
        $comment = $request->get('comment');
        $status = $request->get('status');

        $missionResult = new MissionResult();
        $missionResult->setUser($userByToken);
        $missionResult->setMission($mission);
        $missionResult->setMissionTitle($mission_title);
        $missionResult->setPoints($points);
        $missionResult->setComment($comment);
        $missionResult->setStatus($status);

        /** @var MissionResultService $service */
        $service = $this->get('your_life.data.mission_result_service');
        try {
            $service->create($missionResult);
            return new JsonResponse(null, 201);
        } catch(\Exception $ex) {
            throw new ApiException(500, ApiExceptionType::ERROR_MISSION_RESULT_CREATE, $ex->getMessage());
        }
    }

    public function addPhotoAction() {

        $request = $this->getRequest();
        $mission_id = $request->get('mission_id');
        $fileBug = $request->files;

        $repo = $this->doctrine->getRepository('YourLifeDataBundle:MissionResult');
        /** @var MissionResult $missionResult */
        $missionResult = $repo->find($mission_id);

        /** @var MissionResultService $service */
        $service = $this->get('your_life.data.mission_result_service');

        $keys = $fileBug->keys();
        foreach($keys as $key) {
            try{
                $service->addPhoto($missionResult, $key);
            } catch(\Exception $ex){
                $error = new PhotoUploadApiException();
                $error->addDetail(new ApiExceptionDetail($ex->getCode(), $ex->getMessage()));

                throw new $error;
            }
        }

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