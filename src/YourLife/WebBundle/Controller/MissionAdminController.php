<?php

namespace YourLife\WebBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use YourLife\DataBundle\Document\Mission;
use YourLife\DataBundle\Document\MissionCloseConditions;
use YourLife\DataBundle\Service\MissionService;

/**
 * Class MissionAdminController
 *
 * @package YourLife\WebBundle\Controller
 */
class MissionAdminController extends Controller
{
    /**
     * Action for displaying list of missions
     */
    public function missionAllAction()
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionRepository */
        $missionRepository = $documentManager->getRepository('YourLifeDataBundle:Mission');

        $missions = $missionRepository->findAll();

        return $this->render(
            'YourLifeWebBundle:MissionAdmin:list.html.twig',
            [
                'missions' => $missions
            ]
        );
    }

    /**
     * Action for creating new mission
     */
    public function missionCreateAction()
    {
        $request = $this->getRequest();

        if ($request->isMethod('post')) {
            /** @var MissionService $missionService */
            $missionService = $this->get('your_life.data.mission_service');

            $mission = new Mission();
            $mission->setTitle($request->request->get('title'));
            $mission->setDescription($request->request->get('description'));
            $mission->setPoints($request->request->getInt('points', 1));
            $mission->setUserLevel($request->request->getInt('user_level', 1));
            $mission->setExecutionTime($request->request->getInt('execution_time', 1));

            $closeConditions = new MissionCloseConditions();
            $closeConditions->setIsNeedComment($request->request->getInt('close_need_comment', 1));
            $closeConditions->setIsNeedPhotos($request->request->getInt('close_need_photos', 1));
            $closeConditions->setText($request->request->get('close_text'));
            $mission->setCloseConditions($closeConditions);

            try {
                $files = $request->files->all();
                /** @var UploadedFile $uploadedFile */
                foreach ($files['file'] as $uploadedFile) {
                    if (is_object($uploadedFile)) {
                        $missionService->addPhoto($mission, $uploadedFile->getPathname(), false);
                    }
                }

                $missionService->create($mission);

                return $this->redirect(
                    $this->generateUrl(
                        'your_life_web_mission_admin_show',
                        [
                            'id' => $mission->getId()
                        ]
                    )
                );
            } catch (\Exception $e) {
                return $this->render("YourLifeWebBundle:MissionAdmin:create.html.twig", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $this->render("YourLifeWebBundle:MissionAdmin:create.html.twig");
    }

    /**
     * Action for removing missions
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function missionRemoveAction($id)
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionRepository */
        $missionRepository = $documentManager->getRepository('YourLifeDataBundle:Mission');

        $mission = $missionRepository->find($id);

        if ($mission) {
            /** @var MissionService $missionService */
            $missionService = $this->get('your_life.data.mission_service');
            $missionService->remove($mission);
        }

        return $this->redirect($this->generateUrl('your_life_web_mission_admin_all'));
    }

    /**
     * Action for displaying mission
     *
     * @param $id
     *
     * @return Response
     */
    public function missionShowAction($id)
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->get('doctrine_mongodb')->getManager('yourlife');

        /** @var DocumentRepository $missionRepository */
        $missionRepository = $documentManager->getRepository('YourLifeDataBundle:Mission');

        $mission = $missionRepository->find($id);

        return $this->render(
            'YourLifeWebBundle:MissionAdmin:show.html.twig',
            [
                'mission' => $mission
            ]
        );
    }
}
