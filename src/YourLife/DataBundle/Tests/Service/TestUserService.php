<?php

namespace YourLife\DataBundle\Tests\Service;

use YourLife\DataBundle\Document\User;
use YourLife\DataBundle\Service\UserService;
use YourLife\DataBundle\Tests\ContainerAwareUnitTestCase;

class TestUserService extends ContainerAwareUnitTestCase
{
    /** @var UserService */
    protected $userService;

    protected function setUp()
    {
        $this->userService = $this->get('your_life.data.user_service');
    }

    public function testCreate()
    {
        $username   = sprintf('new_user_%s',  uniqid());
        $password   = 'new_password';
        $email      = sprintf('%s@sixdays.ru', uniqid());

        $user       = $this->userService->create($username, $password, $email);

        $this->assertNotEquals(null, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());

        return $user;
    }

    /**
     * @depends testCreate
     */
    public function testFindByCredentials(User $user)
    {
        $password           = 'new_password';
        $invalidPassword    = 'old_password';

        $findedUser     = $this->userService->findByCredentials($user->getUsername(), $password);
        $notFoundUser   = $this->userService->findByCredentials($user->getUsername(), $invalidPassword);

        $this->assertNotEquals(null, $findedUser);
        $this->assertEquals(null, $notFoundUser);

        $this->userService->remove($findedUser);
    }
} 