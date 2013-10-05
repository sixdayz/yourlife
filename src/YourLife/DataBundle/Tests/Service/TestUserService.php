<?php

namespace YourLife\DataBundle\Tests\Service;

use YourLife\DataBundle\Service\UserService;
use YourLife\DataBundle\Tests\ContainerAwareUnitTestCase;

class TestUserService extends ContainerAwareUnitTestCase
{
    public function testCreate()
    {
        /** @var UserService $userService */
        $userService = $this->get('your_life.data.user_service');

        $username   = sprintf('new_user_%s',  uniqid());
        $password   = 'new_password';
        $email      = sprintf('%s@sixdays.ru', uniqid());

        $user       = $userService->create($username, $password, $email);

        $this->assertNotEquals(null, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());
    }
} 