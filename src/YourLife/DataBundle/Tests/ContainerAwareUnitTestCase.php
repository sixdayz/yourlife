<?php

namespace YourLife\DataBundle\Tests;

require_once __DIR__.'/../../../../app/AppKernel.php';

class ContainerAwareUnitTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('dev', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }

    public function get($serviceId)
    {
        return self::$container->get($serviceId);
    }

    public function getParameter($parameterId)
    {
        return self::$container->getParameter($parameterId);
    }
} 