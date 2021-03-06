<?php

namespace YourLife\ApiBundle\Controller;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Tests\Iterator\DateRangeFilterIteratorTest;
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
    protected $user_service;

    /** @var ManagerRegistry $managerRegistry */
    protected $managerRegistry;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);

        $this->user_service = $this->get('your_life.api.user_service');
        $this->managerRegistry = $this->get('doctrine_mongodb');
    }

    /**
     * Текущие миссии пользователя
     * */
    public function getListAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('x-session-token');

        $this->user_service->getUserByToken($token);
        $user = $this->user_service->getUserById($user_id);

        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:MissionResult');
        $missionResults = $repo->findBy([
            '$or' => [
                ['status' => MissionResultStatus::IN_PROGRESS],
                ['status' => MissionResultStatus::COMPLETE]
            ],
            'user' => $user_id
        ]);

        $result = $this->convertMissionResults($missionResults);

        return new JsonResponse($result, 200);
    }

    /**
     * Получаем доступные миссии для пользователя $user_id
     * */
    public function getAvailableMissionsAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('x-session-token');

        $userByToken = $this->user_service->getUserByToken($token);
        $userById = $this->user_service->getUserById($user_id);

        if($userById != $userByToken) {
            throw new AccessErrorApiException();
        }

        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:Mission');

        $missions = $repo->findBy([
            'user_level' => $userById->getLevel()
        ]);

        $result = $this->convertMissions($missions);
        return new JsonResponse($result, 200);
    }

    /**
     * Получение миссии $mission_id выполняемой пользователем $user_id
     * */
    public function getAction() {
        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('x-session-token');

        $userByToken = $this->user_service->getUserByToken($token);
        $userById = $this->user_service->getUserById($user_id);

//        if($userByToken != $userById) {
//
//        }

        // todo: просматривать только миссии пользователя

        $mission_id = $request->get('mission_id');
        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:Mission');
        $mission = $repo->find($mission_id);

        $result = $this->convertMissionToArray($mission, true, $user_id);

        return new JsonResponse($result, 200);
    }

    public function createResultAction() {

        $request = $this->getRequest();

        $user_id = $request->get('user_id');
        $token = $request->headers->get('x-session-token');

        $userByToken = $this->user_service->getUserByToken($token);
        $userById = $this->user_service->getUserById($user_id);

        if($userByToken != $userById) {
            throw new AccessErrorApiException();
        }

        $mission_id = $request->get('mission_id');
        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:Mission');
        $mission = $repo->findOneBy([
            'id' => $mission_id,
            'user_level' => $userById->getLevel()
        ]);

        if($mission == null) {
            throw new MissionNotFoundApiException();
        }

        $mission_title = $request->get('mission_title', '');
        $points = $request->get('points', '');
        $comment = $request->get('comment', '');
        $status = $request->get('status', '');

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
            return new JsonResponse([
                'mission_result_id' => $missionResult->getId()
            ], 201);
        } catch(\Exception $ex) {
            throw new ApiException(500, ApiExceptionType::ERROR_MISSION_RESULT_CREATE, $ex->getMessage());
        }
    }

    public function addPhotoAction() {

        $request = $this->getRequest();
        $mission_id = $request->get('mission_id');
        $fileBug = $request->files;

        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:MissionResult');

        /** @var MissionResult $missionResult */
        $missionResult = $repo->find($mission_id);
        if($missionResult == null) {
            throw new MissionNotFoundApiException();
        }

        /** @var MissionResultService $service */
        $service = $this->get('your_life.data.mission_result_service');

        $files = $request->files->all();
        foreach($files as $file) {

            try{
                $service->addPhoto($missionResult, $file->getPathName());
            } catch(\Exception $ex){
                $error = new PhotoUploadApiException();
                $error->addDetail(new ApiExceptionDetail($ex->getCode(), $ex->getMessage()));

                throw new $error;
            }
        }

        return new JsonResponse();
    }

    public function updateResultAction() {

        $request = $this->getRequest();
        $mission_id = $request->get('mission_id');

        $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:MissionResult');

        /** @var MissionResult $missionResult */
        $missionResult = $repo->find($mission_id);
        if($missionResult == null) {
            throw new MissionNotFoundApiException();
        }

        $missionResult->setStatus($request->get('status'));

        try {
            /** @var ObjectManager $manager */
            $manager = $this->managerRegistry->getManager('yourlife');
            $manager->persist($missionResult);
            $manager->flush();

            return new JsonResponse(null, 201);
        } catch(\Exception $ex) {
            throw new ApiException(500, ApiExceptionType::ERROR_MISSION_RESULT_UPDATE, $ex->getMessage());
        }
    }

    private function convertMissions($missions) {
        $result = [];

        /** @var Mission $mission */
        foreach($missions as $mission) {
            $result[] = $this->convertMissionToArray($mission);
        }

        return $result;
    }

    private function convertMissionToArray($mission, $need_add_status = false, $user_id = '') {

        $path = $this->container->getParameter('yourlife.api.upload_photo_fullpath');

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

        $result = [
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

        if($need_add_status) {
            $repo = $this->managerRegistry->getRepository('YourLifeDataBundle:MissionResult');
            $res = $repo->findBy([
                'mission' => $mission->getId(),
                'user' => $user_id
            ], [
                'create_date' => -1
            ], 1);

            $arr = array_values(iterator_to_array($res));

            $result['mission_result_id'] = '';
            if(count($arr) == 0) {
                $result['status'] = MissionResultStatus::AVAILABLE;
            } else {
                if($arr[0]->getStatus() == MissionResultStatus::USER_CANCELED ||
                    $arr[0]->getStatus() == MissionResultStatus::COMPLETE
                ) {
                    $result['status'] = MissionResultStatus::AVAILABLE;
                } else {
                    $result['mission_result_id'] = $arr[0]->getId();
                    $result['status'] = $arr[0]->getStatus();
                }
            }
        }

        return $result;
    }

    private function convertMissionResults($missionResults) {
        $result = [];

        /** @var MissionResult $missionResult */
        foreach($missionResults as $missionResult) {
            $result[] = $this->convertMissionResultToArray($missionResult);
        }

        return $result;
    }

    private function convertMissionResultToArray($missionResult) {

        $path = $this->container->getParameter('yourlife.api.upload_photo_fullpath');

        $resultPhotoList = [];
        $photoList = $missionResult->getPhotos();
        /** @var Photo $photo */
        foreach($photoList as $photo) {
            $resultPhotoList[] = [
                'id' => $photo->getId(),
                'small' => sprintf('%s/%s', $path, $photo->getSmall()),
                'medium' => sprintf('%s/%s', $path, $photo->getMedium()),
                'origin' => sprintf('%s/%s', $path, $photo->getOrigin())
            ];
        }

        $result = [
            'id' => $missionResult->getId(),
            'mission_id' => $missionResult->getMission()->getId(),
            'mission_title' => $missionResult->getMissionTitle(),
            'points' => $missionResult->getPoints(),
            'comment' => $missionResult->getComment(),
            'photos' => $resultPhotoList,
            'status' => $missionResult->getStatus()
        ];

        return $result;
    }
}