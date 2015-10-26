<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAccess()
    {
        $manager = new Manager();
        for ($i = 0; $i < 10; $i++) {
            $manager[] = new Configuration(sprintf('127.0.0.%d', $i));
        }
        $this->assertSame(10, count($manager));
        for ($i = 5; $i < 10; $i++) {
            unset($manager[$i]);
        }
        $this->assertSame(5, count($manager));
        for ($i = 0; $i < 5; $i++) {
            $manager->removeConnection($i);
        }
        $this->assertSame(0, count($manager));
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     * @expectedExceptionMessage No connections defined!
     */
    public function testEmpty()
    {
        $manager = new Manager();
        $manager->getConfiguration();
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     * @expectedExceptionMessage No connection defined by name:
     */
    public function testNotDefined()
    {
        $manager = new Manager();
        $manager->addConfiguration('a', new Configuration(null));
        $manager->getConfiguration('b');
    }

    public function testDefault()
    {
        $manager = new Manager();
        for ($i = 0; $i < 10; $i++) {
            $manager[] = new Configuration(sprintf('127.0.0.%d', $i));
        }
        $this->assertSame('127.0.0.0', $manager->getConfiguration()->getHost());
    }

    public function testIteration()
    {
        $manager = new Manager();

        for ($i = 0; $i < 10; $i++) {
            $manager[] = new Configuration(sprintf('127.0.0.%d', $i));
            $this->assertTrue(isset($manager[$i]));
        }

        foreach ($manager as $key => $connection) {
            $this->assertSame($manager[$key], $connection);
        }
    }
}