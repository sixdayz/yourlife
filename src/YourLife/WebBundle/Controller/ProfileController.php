<?php

namespace YourLife\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use YourLife\DataBundle\Document\User;
use YourLife\DataBundle\Service\UserLevelService;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;

class ProfileController extends Controller
{
    /** @var UserLevelService */
    protected $userLevel;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ObjectRepository */
    protected $missionResultRepository;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->userLevel                = $this->get('your_life.data.user_level_service');
        $this->managerRegistry          = $this->get('doctrine_mongodb');
        $this->missionResultRepository  = $this->managerRegistry
            ->getManager('yourlife')
            ->getRepository('YourLifeDataBundle:MissionResult');
    }

    public function getAction()
    {
        /** @var User $user */
        $user               = $this->getUser();
        $percentOnLevel     = $this->userLevel->getPercentForLevelPoints($user->getPoints());

        $missionResults     = $this->missionResultRepository->findBy(
            ['user' => $user->getId()],
            ['create_date' => -1]
        );

        return $this->render(
            'YourLifeWebBundle:Profile:get.html.twig',
            [ 'percent_on_level' => $percentOnLevel, 'mission_results' => $missionResults ]
        );
    }
} 