<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;
use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\ConfigurationInterface;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    use Traits\BeanstalkTrait;
    use Traits\DispatcherMockTrait;

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     * @expectedExceptionMessage Unable to connect
     */
    public function testConnection()
    {
        $manager = new Manager();
        $manager->addConfiguration('default', new Configuration('127.0.0.100', 123456, 2, true));
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Server\Configuration', $manager->getConfiguration('default'));
        $this->assertTrue($manager->getConfiguration('default')->isPersistent());
        $this->assertSame(123456, $manager->getConfiguration('default')->getPort());
        $this->assertSame(2, $manager->getConfiguration('default')->getTimeout());
        $manager->getConfiguration('default')->getConnection();
    }

    public function testWrite()
    {
        $dispatcher = $this->getNewMockDispatcherCallable([
            function($name, Event $event){
                $this->assertSame(BeanstalkEvents::STREAM_WRITE, $name);
                $this->assertSame("foo\nbar", $event->getWrite());
                $this->assertTrue((bool) preg_match('/\d+\.\d+/', $event->getTime()));
            },
            function($name, Event $event){
                $this->assertSame(BeanstalkEvents::STREAM_READ, $name);
                $this->assertSame("foo\n", $event->getRead());
                $this->assertTrue((bool) preg_match('/\d+\.\d+/', $event->getTime()));
            },
            function($name, Event $event){
                $this->assertSame(BeanstalkEvents::STREAM_READ, $name);
                $this->assertSame("bar", $event->getRead());
                $this->assertTrue((bool) preg_match('/\d+\.\d+/', $event->getTime()));
            },
        ]);

        $connection = (new Connection())->setSocket(fopen('php://memory', 'r+'));
        $connection->setDispatcher($dispatcher);
        $configuration = new Configuration(null);
        $configuration->setConnection($connection);
        $manager = (new Manager())->addConfiguration('default', $configuration);

        $this->assertTrue($manager->getConfiguration()->getConnection()->hasDispatcher());
        $manager->getConfiguration()->getConnection()->write("foo\nbar");
        $manager->getConfiguration()->getConnection()->rewind();
        $manager->getConfiguration()->getConnection()->readLine();
        $manager->getConfiguration()->getConnection()->read(3);
    }

    /**
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     * @expectedExceptionMessage No connection defined by name: "default"
     */
    public function testRemoveConnection()
    {
        $manager = new Manager();
        $manager->addConfiguration('default', new Configuration('127.0.0.100',123456,2,true));
        unset($manager['default']);
        unset($manager['default']);
    }

    public function testReconnect()
    {
        $configuration = $this->getNewBeanstalk()->getConnectionManager()->getConfiguration();

        $this->assertInstanceOf(
            'PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConnectionTestHelper',
            $configuration->getConnection()
        );

        $configuration->reconnect();

        $this->assertInstanceOf(
            'PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConnectionTestHelper',
            $configuration->getConnection()
        );
    }

}