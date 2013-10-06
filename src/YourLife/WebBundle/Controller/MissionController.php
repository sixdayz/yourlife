<?php

namespace YourLife\WebBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

        return $this->render(
            'YourLifeWebBundle:Mission:list.html.twig',
            [
                'missions' => $missionRepository->findAll()
            ]
        );
    }

    /**
     *
     */
    public function missionGetActiveAction() {

    }

    /**
     *
     */
    public function missionGetHistoryAction() {

    }

    /**
     * @param $id
     */
    public function missionStartAction($id)
    {
    }

    /**
     * @param $id
     */
    public function missionSubmitAction($id)
    {
    }

    /**
     * @param $id
     */
    public function missionRejectAction($id)
    {
    }
}
