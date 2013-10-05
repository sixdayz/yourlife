<?php

namespace YourLife\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('YourLifeWebBundle:Default:index.html.twig', array('name' => $name));
    }
}
