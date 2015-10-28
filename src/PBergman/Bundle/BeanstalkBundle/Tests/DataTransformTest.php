<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Event\DataTransformSubscriber;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;
use PBergman\Bundle\BeanstalkBundle\Server\Manager;
use PBergman\Bundle\BeanstalkBundle\Service\Beanstalk;
use PBergman\Bundle\BeanstalkBundle\Transformer\DataHeader;
use PBergman\Bundle\BeanstalkBundle\Transformer\DataTransformer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DataTransformTest extends \PHPUnit_Framework_TestCase
{

    public function testSubscriber()
    {

        $writeLine = null;
        $writeCount = 0;
        $connection = $this->getMock(Connection::class);
        $connection
            ->expects($this->exactly(4))
            ->method('write')
            ->willReturnCallback(function($data, $size) use (&$writeLine, &$writeCount) {
                if ($writeCount === 2) {
                    $writeLine = $data;
                }
                $writeCount++;
            });

        $connection
            ->expects($this->once())
            ->method('readLine')
            ->willReturn(ResponseInterface::RESPONSE_INSERTED)
        ;

        $object = new \stdClass();
        $object->foo = 'bar';
        $object->bar = 'foo';

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new DataTransformSubscriber());
        $configuration = (new Configuration(null))->setConnection($connection);
        $manager = (new Manager())->addConfiguration('default', $configuration);
        $beanstalk = new Beanstalk($manager, $dispatcher);
        $beanstalk->getProducer()->put($object);

        $unpacked = DataTransformer::unpack($writeLine);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Transformer\DataContainer', $unpacked);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Transformer\DataHeader', $unpacked->getHeader());
        $this->assertSame(posix_getpid(), $unpacked->getHeader()->getPid());
        $this->assertSame('default', $unpacked->getHeader()->getName());
        $this->assertSame($object->foo, $unpacked->getData()->foo);
        $this->assertSame($object->bar, $unpacked->getData()->bar);
        $this->assertSame(DataHeader::T_OBJECT, $unpacked->getHeader()->getType());


        $errorLine  = substr($writeLine, 0, 2);
        $errorLine .= 'AB';
        $errorLine .= substr($writeLine, 4);

        try {
            DataTransformer::unpack($errorLine);
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\TransformerException $e) {
            $this->assertRegExp('/Signature mismatch 0x[a-f0-9]{8} !== 0x[a-f0-9]{8}/', $e->getMessage());
        }

        $errorLine  = $writeLine . 'aaaa';

        try {
            DataTransformer::unpack($errorLine);
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\TransformerException $e) {
            $this->assertRegExp('/CRC mismatch 0x[a-f0-9]{8} !== 0x[a-f0-9]{8}/', $e->getMessage());
        }
    }

    public function testTransformer()
    {
        $transformer = new DataTransformer();

        $transformer
            ->setCompressed(false)
            ->setName('test_string')
            ->setData('foo')
        ;
        $this->assertSame('test_string', $transformer->getName());
        $this->assertSame('foo', $transformer->getData());
        $this->assertFalse($transformer->isCompressed());
        $packed = $transformer->pack();
        $this->assertTrue(is_string($packed));
        $unpacked = $transformer->unpack($packed);
        $this->assertSame(DataHeader::T_STRING, $unpacked->getHeader()->getType());
        $this->assertFalse($unpacked->getHeader()->isCompressed());
        $this->assertTrue(is_string($unpacked->getData()));

        $transformer
            ->setCompressed(true)
            ->setName('test_array')
            ->setData(['foo', 'bar'])
        ;
        $this->assertSame('test_array', $transformer->getName());
        $this->assertTrue($transformer->isCompressed());
        $packed = $transformer->pack();
        $unpacked = $transformer->unpack($packed);
        $this->assertSame(DataHeader::T_ARRAY, $unpacked->getHeader()->getType());
        $this->assertTrue(is_array($unpacked->getData()));

        $transformer
            ->setCompressed(true)
            ->setName('test_int')
            ->setData(1)
        ;
        $packed = $transformer->pack();
        $unpacked = $transformer->unpack($packed);
        $this->assertSame(DataHeader::T_INTEGER, $unpacked->getHeader()->getType());
        $this->assertTrue(is_int($unpacked->getData()));
        $this->assertEquals($unpacked->getData(), 1);

        $transformer
            ->setCompressed(true)
            ->setName('test_double')
            ->setData(2.2)
        ;
        $packed = $transformer->pack();
        $unpacked = $transformer->unpack($packed);
        $this->assertSame(DataHeader::T_DOUBLE, $unpacked->getHeader()->getType());
        $this->assertTrue(is_double($unpacked->getData()));
        $this->assertEquals($unpacked->getData(), 2.2);


        $transformer
            ->setCompressed(true)
            ->setName('test_bool')
            ->setData(true)
        ;
        $packed = $transformer->pack();
        $unpacked = $transformer->unpack($packed);
        $this->assertSame(DataHeader::T_BOOLEAN, $unpacked->getHeader()->getType());
        $this->assertTrue(is_bool($unpacked->getData()));
        $this->assertEquals($unpacked->getData(), true);


        $transformer
            ->setCompressed(true)
            ->setName('test_bool')
            ->setData(fopen('php://memory', 'r+'))
        ;
        try {
            $transformer->pack();
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\TransformerException $e) {
            $this->assertRegExp('/Unsupported type given: "[^"]+"/', $e->getMessage());
        }
    }

    public function testPostSubscriber()
    {
        $transformer = new DataTransformer();
        $transformer
            ->setCompressed(true)
            ->setName('test_bool')
            ->setData(true)
        ;
        $packed = $transformer->pack();
        $connection = $this->getMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('readLine')
            ->willReturn(sprintf(
                "%s 10 %d\r\n",
                ResponseInterface::RESPONSE_RESERVED,
                strlen($packed)
            ))
        ;

        $connection
            ->expects($this->once())
            ->method('read')
            ->willReturn(sprintf(
                "%s\r\n",
                $packed
            ))
        ;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new DataTransformSubscriber());
        $configuration = (new Configuration(null))->setConnection($connection);
        $manager = (new Manager())->addConfiguration('default', $configuration);
        $beanstalk = new Beanstalk($manager, $dispatcher);
        $response = $beanstalk->getWorker()->reserve();
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Transformer\DataContainer', $response->getData());
        $this->assertTrue( $response->getData()->getData());
    }
}