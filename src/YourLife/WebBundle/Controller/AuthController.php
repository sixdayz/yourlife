<?php

namespace YourLife\WebBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;
use YourLife\DataBundle\Document\User;
use YourLife\DataBundle\Service\UserService;

/**
 * Class AuthController
 *
 * @package YourLife\WebBundle\Controller
 */
class AuthController extends Controller
{
    /**
     *
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'YourLifeWebBundle:Auth:auth.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error' => $error,
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        $request = $this->getRequest();

        if ($request->isMethod('post')) {
            /** @var UserService $userService */
            $userService = $this->get('your_life.data.user_service');

            try {
                $userService->create(
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $request->request->get('email')
                );

                return $this->redirect($this->generateUrl('your_life_web_login'));
            } catch (\Exception $e) {
                return $this->render("YourLifeWebBundle:Auth:register.html.twig", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $this->render("YourLifeWebBundle:Auth:register.html.twig");
    }
}
