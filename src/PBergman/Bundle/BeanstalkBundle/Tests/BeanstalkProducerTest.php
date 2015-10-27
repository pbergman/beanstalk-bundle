<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConnectionTestHelper;
use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;

class BeanstalkProducerTest extends \PHPUnit_Framework_TestCase
{
    use Traits\BeanstalkTrait;
    use Traits\DispatcherMockTrait;
    use Traits\StringTrait;

    public function putPushProvider()
    {
        $count = (func_num_args() > 0) ? func_get_arg(0) : 10;
        $ret = [];

        for ($i = 0; $i < $count; $i++) {
            $ret[] = [
                trim($this->getRandomString(mt_rand(100, 400))),
                rand(1,10),
                rand(1,10),
                rand(1,10),
            ];
        }

        return $ret;
    }

    public function UseTubeProvider()
    {
        $count = (func_num_args() > 0) ? func_get_arg(0) : 10;
        $ret = [];
        for ($i = 0; $i < $count; $i++) {
            $ret[] = [
                trim($this->getRandomString(mt_rand(100, 200), 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')),
            ];
        }
        return $ret;
    }

    /**
     * @dataProvider putPushProvider
     */
    public function testPut($data, $priority, $delay, $ttr)
    {
        $dispatcher = $this->getNewMockDispatcherCallable([
            function($name, $event) use ($data, $priority ,$delay, $ttr) {
                $this->assertSame($name, BeanstalkEvents::PRE_DISPATCH_PUT);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent', $event);
                $this->assertSame('put', $event->getCommand());
                $this->assertSame([$data, $priority ,$delay, $ttr, 'default'], $event->getPayload());
            },
            function($name, $event){
                $this->assertSame($name, BeanstalkEvents::POST_DISPATCH_PUT);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent', $event);
                $this->assertTrue($event->getResponse()->isSuccess());
            },
        ]);

        $producer = $this->getNewBeanstalk($dispatcher)->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->reset();
        $connection->writeReponse("INSERTED 1\r\n");
        $response = $producer->put($data, $priority ,$delay, $ttr);

        $result =  [
            sprintf("put %s %s %s %d\r\n", $priority ,$delay, $ttr, strlen($data)),
            sprintf("%s\r\n", $data)
        ];
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PutResponse', $response);
        $this->assertSame($result, $connection->getHistory());
    }

    /**
     * @depends                  testPut
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException
     * @expectedExceptionMessage The server ran out of memory trying to grow the priority queue data structure.
     */
    public function testPutBuried()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        try {
            $connection->reset();
            $connection->writeReponse("BURIED 99\r\n");
            $producer->put(...$this->putPushProvider(1)[0]);
        } catch(\PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException $e) {
            $this->assertSame(99, (int) $e->getResponse()->getData());
            $this->assertSame('BURIED', $e->getResponse()->getResponse());
        }
        $connection->reset();
        $connection->writeReponse("BURIED 1\r\n");
        $response = $producer->put(...$this->putPushProvider(1)[0]);
        $this->assertEquals(1, $response->getData());

    }


    /**
     * @depends                  testPut
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException
     * @expectedExceptionMessage The job body must be followed by a CR-LF pair, that is, "\r\n".
     */
    public function testPutExpectedCRLF()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("EXPECTED_CRLF\r\n");
        $producer->put(...$this->putPushProvider(1)[0]);
    }

    /**
     * @depends                  testPut
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException
     * @expectedExceptionMessage The client has requested to put a job with a body larger than max-job-size bytes
     */
    public function testPutJobToBig()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("JOB_TOO_BIG\r\n");
        $producer->put(...$this->putPushProvider(1)[0]);
    }

    /**
     * @depends                  testPut
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException
     * @expectedExceptionMessage The server has been put into "drain mode" and is no longer accepting new
     */
    public function testPutDraining()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("DRAINING\r\n");
        $producer->put(...$this->putPushProvider(1)[0]);
    }

    /**
     * @depends                  testPut
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPutUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->put('foo');
    }

    /**
     * @dataProvider UseTubeProvider
     */
    public function testUseTube($tube)
    {
        $dispatcher = $this->getNewMockDispatcherCallable([
            function($name, $event) {
                $this->assertSame($name, BeanstalkEvents::PRE_DISPATCH_USE);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent', $event);
                $this->assertSame('use', $event->getCommand());
            },
            function($name, $event){
                $this->assertSame($name, BeanstalkEvents::POST_DISPATCH_USE);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent', $event);
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\UseResponse', $event->getResponse());
                $this->assertTrue($event->getResponse()->isSuccess());
            },
        ]);
        $producer = $this->getNewBeanstalk($dispatcher)->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->reset();
        $connection->writeReponse(sprintf("USING %s\r\n", $tube));
        $producer->useTube($tube);
        $this->assertEquals($producer->getUsingTube(), $tube);
        $this->assertSame(sprintf("use %s\r\n", $tube), $connection->getHistory()[0]);

        try {
            $producer->useTube('-bar');
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException $e) {
            $this->assertRegExp('/Invalid tube name: "-bar"/', $e->getMessage());
        }

    }



}