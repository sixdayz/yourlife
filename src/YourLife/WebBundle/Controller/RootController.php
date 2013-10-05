<?php

namespace YourLife\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RootController extends Controller
{
    public function indexAction()
    {
        return new Response('Life is good!');
    }
}
