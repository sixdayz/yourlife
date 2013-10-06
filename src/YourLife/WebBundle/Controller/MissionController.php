<?php

namespace YourLife\WebBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionResult;
use YourLife\DataBundle\Enum\MissionResultStatus;
use YourLife\DataBundle\Service\MissionResultService;

/**
 * Class MissionController
 *
 * @package YourLife\WebBundle\Controller
 */
class MissionController extends Controller
{
    /**
     *
     */
    public function missionGetAvailableAction()
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionRepository */
        $missionRepository = $documentManager->getRepository('YourLifeDataBundle:Mission');

        $missions = $missionRepository->findAll();

        return $this->render(
            'YourLifeWebBundle:Mission:list.html.twig',
            [
                'missions' => $missions
            ]
        );
    }

    /**
     *
     */
    public function missionGetActiveAction()
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionResultRepository */
        $missionResultRepository = $documentManager->getRepository('YourLifeDataBundle:MissionResult');

        $missionResults = $missionResultRepository->findBy(
            [
                'user' => $this->getUser()->getId(),
                'status' => MissionResultStatus::IN_PROGRESS
            ]
        );

        return $this->render(
            'YourLifeWebBundle:Mission:active.html.twig',
            [
                'results' => $missionResults
            ]
        );
    }

    /**
     *
     */
    public function missionGetCompleteAction()
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionResultRepository */
        $missionResultRepository = $documentManager->getRepository('YourLifeDataBundle:MissionResult');

        $missionResults = $missionResultRepository->findBy(
            [
                'user' => $this->getUser()->getId(),
                'status' => MissionResultStatus::COMPLETE
            ]
        );

        return $this->render(
            'YourLifeWebBundle:Mission:complete.html.twig',
            [
                'results' => $missionResults
            ]
        );
    }

    /**
     *
     */
    public function missionGetCanceledAction()
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionResultRepository */
        $missionResultRepository = $documentManager->getRepository('YourLifeDataBundle:MissionResult');

        $missionResults = $missionResultRepository->findBy(
            [
                'user' => $this->getUser()->getId(),
                'status' => MissionResultStatus::USER_CANCELED
            ]
        );

        return $this->render(
            'YourLifeWebBundle:Mission:canceled.html.twig',
            [
                'results' => $missionResults
            ]
        );
    }


    public function missionAction($id)
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        switch ($this->getRequest()->get('mission_result_action')) {
            case "accept":
                /** @var DocumentRepository $missionRepository */
                $missionRepository = $documentManager->getRepository('YourLifeDataBundle:Mission');

                /** @var Mission|null $mission */
                $mission = $missionRepository->find($id);

                /** @var MissionResultService $missionResultService */
                $missionResultService = $this->get('your_life.data.mission_result_service');

                $missionResult = new MissionResult();
                $missionResult->setStatus(MissionResultStatus::IN_PROGRESS);
                $missionResult->setUser($this->getUser());
                $missionResult->setMission($mission);
                $missionResult->setMissionTitle($mission->getTitle());

                $missionResultService->create($missionResult);
                break;
            case "submit":
                /** @var DocumentRepository $missionResultRepository */
                $missionResultRepository = $documentManager->getRepository('YourLifeDataBundle:MissionResult');

                /** @var MissionResult|null $missionResult */
                $missionResult = $missionResultRepository->find($id);
                $missionResult->setStatus(MissionResultStatus::COMPLETE);
                $documentManager->persist($missionResult);
                $documentManager->flush($missionResult);
                break;
            case "cancel":
                /** @var DocumentRepository $missionResultRepository */
                $missionResultRepository = $documentManager->getRepository('YourLifeDataBundle:MissionResult');

                /** @var MissionResult|null $missionResult */
                $missionResult = $missionResultRepository->find($id);
                $missionResult->setStatus(MissionResultStatus::USER_CANCELED);
                $documentManager->persist($missionResult);
                $documentManager->flush($missionResult);
                break;
        }

        return $this->redirect($this->generateUrl('your_life_web_mission_get_active'));
    }
}
