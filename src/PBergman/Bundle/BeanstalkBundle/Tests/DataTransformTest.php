<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Event\DataTransformSubscriber;
use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;
use PBergman\Bundle\BeanstalkBundle\Service\Beanstalk;
use PBergman\Bundle\BeanstalkBundle\Transformer\DataTransformer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DataTransformTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscriber()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $object->bar = 'foo';

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new DataTransformSubscriber());
        $connection = (new Connection())
            ->setSocket(fopen('php://memory', 'r+'))
        ;
        $configuration = (new Configuration(null))->setConnection($connection);
        $manager = (new Manager())->addConfiguration('default', $configuration);
        $beanstalk = new Beanstalk($manager, $dispatcher);
        $data = [];

        try {
            $beanstalk->getProducer()->put($object);
        } catch (\Exception $e) {
            $connection->rewind();
            $line = explode(' ', trim($connection->readLine()));
            $data = trim($connection->read(end($line)));
        }

        $cdata = DataTransformer::unpack($data);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Transformer\DataContainer', $cdata);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Transformer\DataHeader', $cdata->getHeader());
        $this->assertSame($object->foo, $cdata->getData()->foo);
        $this->assertSame($object->bar, $cdata->getData()->bar);
    }
}