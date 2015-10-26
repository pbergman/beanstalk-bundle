<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use PBergman\Bundle\BeanstalkBundle\Tests\Helper\ConnectionTestHelper;
use PBergman\Bundle\BeanstalkBundle\Service;

class BeanstalkWorkerTest extends \PHPUnit_Framework_TestCase
{
    use Traits\BeanstalkTrait;
    use Traits\DispatcherMockTrait;
    use Traits\StringTrait;

    public function tubeNameProvider()
    {
        $count = (func_num_args() > 0) ? func_get_arg(0) : 10;
        $ret = [];
        for ($i = 0; $i < $count; $i++) {
            $ret[] = [
                trim($this->getRandomString(mt_rand(100, 200), 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')),
                mt_rand(10, 200)
            ];
        }
        return $ret;
    }

    /**
     * @dataProvider tubeNameProvider
     */
    public function testReserve($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_RESERVE,
            BeanstalkEvents::POST_DISPATCH_RESERVE,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("RESERVED %d %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->reserve();
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ReserveResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_RESERVED, $response->getResponse());
        $this->assertSame("reserve\r\n", $connection->getHistory()[0]);
    }

    /**
     * @dataProvider tubeNameProvider
     */
    public function testReserveTimeout($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_RESERVE_WITH_TIMEOUT,
            BeanstalkEvents::POST_DISPATCH_RESERVE_WITH_TIMEOUT,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("RESERVED %d %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->reserve($id*3);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ReserveResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_RESERVED, $response->getResponse());
        $this->assertSame(sprintf("reserve-with-timeout %d\r\n", $id*3), $connection->getHistory()[0]);
    }

    /**
     * @depends                   testReserve
     * @expectedException         \PBergman\Bundle\BeanstalkBundle\Exception\ResponseReserveException
     * @expectedExceptionMessage  The client issues a reserve command during the safety margin, or the
     */
    public function testReserveDeadlineSoon()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("DEADLINE_SOON");
        $worker->reserve();
    }

    /**
     * @depends                   testReserve
     * @expectedException         \PBergman\Bundle\BeanstalkBundle\Exception\ResponseReserveException
     * @expectedExceptionMessage  A non-negative timeout was specified and the timeout exceeded before
     */
    public function testReserveTimedOut()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("TIMED_OUT");
        $worker->reserve();
    }

    /**
     * @depends                         testReserve
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testReserveUnKnownResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->reserve();
    }

    public function testDelete()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_DELETE,
            BeanstalkEvents::POST_DISPATCH_DELETE,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("DELETED\r\n");
        $response = $worker->delete(10);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\DeleteResponse', $response);
        $this->assertSame(10, $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_DELETED, $response->getResponse());
        $this->assertSame("delete 10\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                   testDelete
     * @expectedException         \PBergman\Bundle\BeanstalkBundle\Exception\ResponseDeleteException
     * @expectedExceptionMessage  The job does not exist or is not either reserved by th
     */
    public function testDeleteNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->delete(10);
    }

    /**
     * @depends                         testDelete
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testDeleteUnKnownResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->delete(1);
    }

    public function testRelease()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_RELEASE,
            BeanstalkEvents::POST_DISPATCH_RELEASE,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("RELEASED\r\n");
        $response = $worker->release(10, 20, 5);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ReleaseResponse', $response);
        $this->assertSame(10, $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_RELEASED, $response->getResponse());
        $this->assertSame("release 10 20 5\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                   testRelease
     * @expectedException         \PBergman\Bundle\BeanstalkBundle\Exception\ResponseReleaseException
     * @expectedExceptionMessage  The server ran out of memory trying to grow the priority queue data structure.
     */
    public function testReleaseBuried()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("BURIED\r\n");

        try {
            $connection->writeReponse("BURIED\r\n");
            $worker->release(10, 20, 5);
        } catch(\PBergman\Bundle\BeanstalkBundle\Exception\ResponseReleaseException $e) {
            $this->assertSame(10, (int) $e->getResponse()->getData());
            $this->assertSame('BURIED', $e->getResponse()->getResponse());

        }
        $connection->reset();
        $connection->writeReponse("BURIED\r\n");
        $worker->release(10, 20, 5);
    }

    /**
     * @depends                         testRelease
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseReleaseException
     * @expectedExceptionMessageRegExp  /^Job \d+ does not exist or is not reserved by the client.$/
     */
    public function testReleaseNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->release(10, 20, 5);
    }

    /**
     * @depends                         testRelease
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testReleasedUnKnowResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->release(10, 20, 5);
    }

    public function testBury()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_BURY,
            BeanstalkEvents::POST_DISPATCH_BURY,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("BURIED\r\n");
        $response = $worker->bury(10, 20);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\BuryResponse', $response);
        $this->assertSame(null, $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_BURIED, $response->getResponse());
        $this->assertSame("bury 10 20\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                         testBury
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseBuryException
     * @expectedExceptionMessageRegExp  /^Job \d+ does not exist or is not reserved by the client.$/
     */
    public function testBuryNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->bury(10, 20);
    }

    /**
     * @depends                         testBury
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testBuryUnKnowResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->bury(10, 20);
    }

    public function testTouch()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_TOUCH,
            BeanstalkEvents::POST_DISPATCH_TOUCH,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("TOUCHED\r\n");
        $response = $worker->touch(10);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\TouchResponse', $response);
        $this->assertSame(null, $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_TOUCHED, $response->getResponse());
        $this->assertSame("touch 10\r\n", $connection->getHistory()[0]);

    }

    /**
     * @depends                         testTouch
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseTouchException
     * @expectedExceptionMessageRegExp  /^Job \d+ does not exist or is not reserved by the client.$/
     */
    public function testTouchNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->touch(10);
    }

    /**
     * @depends                         testTouch
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testTouchUnKnowResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->touch(10);
    }

    /**
     * @dataProvider tubeNameProvider
     * @param   string  $tube
     * @param   int     $count
     */
    public function testWatch($tube, $count)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_WATCH,
            BeanstalkEvents::POST_DISPATCH_WATCH,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf('WATCHING %u\r\n', $count));
        $response = $worker->watch($tube);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\WatchResponse', $response);
        $this->assertSame($count, (int) $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_WATCHING, $response->getResponse());
        $this->assertSame(sprintf("watch %s\r\n", $tube), $connection->getHistory()[0]);
    }

    /**
     * @dataProvider tubeNameProvider
     * @param   string  $tube
     * @param   int     $count
     */
    public function testIgnore($tube, $count)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_IGNORE,
            BeanstalkEvents::POST_DISPATCH_IGNORE,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("WATCHING %u\r\n", $count));
        $response = $worker->ignore($tube);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\IgnoreResponse', $response);
        $this->assertSame($count, (int) $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_WATCHING, $response->getResponse());
        $this->assertSame(sprintf("ignore %s\r\n", $tube), $connection->getHistory()[0]);

    }

    /**
     * @depends                  testIgnore
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseIgnoreException
     * @expectedExceptionMessage Trying to ignore the only tube in its watch list.
     */
    public function testIgnoreNotIgnored()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_IGNORED\r\n");
        $worker->ignore('default10');
    }

    /**
     * @depends                         testIgnore
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessageRegExp  /Unknown response: "[^"]+"/
     */
    public function testIgnoreUnKnowResponse()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("UNKNOWN_RESPONSE\r\n");
        $worker->ignore('default10');
    }
}