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
use Symfony\Component\Yaml\Yaml;

class BeanstalkDefaultTest extends \PHPUnit_Framework_TestCase
{
    use Traits\BeanstalkTrait;
    use Traits\StringTrait;
    use Traits\DispatcherMockTrait;

    public function dataNameProvider()
    {
        $count = (func_num_args() > 0) ? func_get_arg(0) : 10;
        $ret = [];
        for ($i = 0; $i < $count; $i++) {
            $ret[] = [
                trim($this->getRandomString(mt_rand(10, 200), 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')),
                mt_rand(10, 200)
            ];
        }
        return $ret;
    }

    /**
     * @dataProvider dataNameProvider
     * @param   string  $data
     * @param   int     $id
     */
    public function testPeek($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_PEEK,
            BeanstalkEvents::POST_DISPATCH_PEEK,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse(sprintf("FOUND %u %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->peek($id);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PeekResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_FOUND, $response->getResponse());
        $this->assertSame(sprintf("peek %u\r\n", $id), $connection->getHistory()[0]);
    }

    /**
     * @depends                  testPeek
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePeekException
     * @expectedExceptionMessage The requested job doesn't exist or there are no jobs in the requested state
     */
    public function testPeekNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->peek(10);
    }

    /**
     * @depends                  testPeek
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPeekUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->peek(10);
    }

    /**
     * @dataProvider dataNameProvider
     * @param   string  $data
     * @param   int     $id
     */
    public function testPeekReady($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_PEEK_READY,
            BeanstalkEvents::POST_DISPATCH_PEEK_READY,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse(sprintf("FOUND %u %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->peekReady();

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PeekReadyResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_FOUND, $response->getResponse());
        $this->assertSame(sprintf("peek-ready\r\n", $id), $connection->getHistory()[0]);
    }

    /**
     * @depends                  testPeekReady
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePeekException
     * @expectedExceptionMessage The requested job doesn't exist or there are no jobs in the requested state
     */
    public function testPeekReadyNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->peekReady();
    }

    /**
     * @depends                  testPeekReady
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPeekReadyUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->peekReady();
    }

    /**
     * @dataProvider dataNameProvider
     * @param   string  $data
     * @param   int     $id
     */
    public function testPeekDelayed($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_PEEK_DELAYED,
            BeanstalkEvents::POST_DISPATCH_PEEK_DELAYED,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse(sprintf("FOUND %u %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->peekDelayed();

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PeekDelayedResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_FOUND, $response->getResponse());
        $this->assertSame(sprintf("peek-delayed\r\n", $id), $connection->getHistory()[0]);
    }

    /**
     * @depends                  testPeekDelayed
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePeekException
     * @expectedExceptionMessage The requested job doesn't exist or there are no jobs in the requested state
     */
    public function testPeekDelayedNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->peekDelayed();
    }

    /**
     * @depends                  testPeekDelayed
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPeekDelayedUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->peekDelayed();
    }

    /**
     * @dataProvider dataNameProvider
     * @param   string  $data
     * @param   int     $id
     */
    public function testPeekBuried($data, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_PEEK_BURIED,
            BeanstalkEvents::POST_DISPATCH_PEEK_BURIED,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse(sprintf("FOUND %u %d\r\n%s\r\n", $id, strlen($data), $data));
        $response = $worker->peekBuried();

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PeekBuriedResponse', $response);
        $this->assertSame($data, $response->getData());
        $this->assertSame($id,   $response->getId());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_FOUND, $response->getResponse());
        $this->assertSame(sprintf("peek-buried\r\n", $id), $connection->getHistory()[0]);
    }

    /**
     * @depends                  testPeekBuried
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePeekException
     * @expectedExceptionMessage The requested job doesn't exist or there are no jobs in the requested state
     */
    public function testPeekBuriedNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->reset();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->peekDelayed();
    }

    /**
     * @depends                  testPeekBuried
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPeekBuriedUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->peekBuried();
    }

    public function testKick()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_KICK,
            BeanstalkEvents::POST_DISPATCH_KICK,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("KICKED 10\r\n");
        $response = $worker->kick(10);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\KickResponse', $response);
        $this->assertSame(10, (int) $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_KICKED, $response->getResponse());
        $this->assertSame("kick 10\r\n", $connection->getHistory()[0]);
    }

    public function testKickJob()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_KICK_JOB,
            BeanstalkEvents::POST_DISPATCH_KICK_JOB,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("KICKED 10\r\n");
        $response = $worker->kickJob(10);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\KickJobResponse', $response);
        $this->assertSame(10, (int) $response->getData());
        $this->assertTrue($response->isSuccess());
        $this->assertSame(ResponseInterface::RESPONSE_KICKED, $response->getResponse());
        $this->assertSame("kick-job 10\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                         testKickJob
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseKickJobException
     * @expectedExceptionMessageRegExp  /\[NOT_FOUND\] Job "[^"]+" does not exist or is not in a kickable state/
     */
    public function testKickJobNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->kickJob(10);
    }

    /**
     * @depends                  testPeekReady
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testKickJobUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->kickJob(10);
    }

    public function testStatsJob()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_STATS_JOB,
            BeanstalkEvents::POST_DISPATCH_STATS_JOB,
        ]);
        $id = mt_rand(10,100);
        $data = Yaml::dump([
            'id'    => $id,
            'tube'  => sprintf('tube[%s]', $id),
            'state' => mt_rand(10,100),
            'pri'   => mt_rand(10,100),
            'age'   => mt_rand(10,100),
            'time-left' => mt_rand(10,100),
            'file' => null,
            'reserves' => mt_rand(10,100),
            'timeouts' => mt_rand(10,100),
            'releases' => mt_rand(10,100),
            'buries' => mt_rand(10,100),
            'kicks' => mt_rand(10,100),
        ]);
        
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("OK %d\r\n%s\r\n", strlen($data), $data));
        $response = $worker->statsJob(10);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\StatsJobResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(Yaml::parse($data), $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_OK, $response->getResponse());
        $this->assertSame("stats-job 10\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                         testStatsJob
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseStatsException
     * @expectedExceptionMessageRegExp  /\[[^\]]+\] Job "[^"]+" does not exist./
     */
    public function testStatsJobNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->statsJob(10);
    }

    /**
     * @depends                  testStatsJob
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testStatsJobUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->statsJob(10);
    }

    /**
     * @dataProvider dataNameProvider
    */
    public function testStatsTube($name, $id)
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_STATS_TUBE,
            BeanstalkEvents::POST_DISPATCH_STATS_TUBE,
        ]);
        $data = Yaml::dump([
            'name' => $name,
            'current-jobs-urgent' => mt_rand(10,100),
            'current-jobs-ready' => mt_rand(10,100),
            'current-jobs-reserved' => mt_rand(10,100),
            'current-jobs-delayed' => mt_rand(10,100),
            'current-jobs-buried' => mt_rand(10,100),
            'total-jobs' => mt_rand(10,100),
            'current-using' => mt_rand(10,100),
            'current-waiting' => mt_rand(10,100),
            'current-watching' => mt_rand(10,100),
            'pause' => mt_rand(10,100),
            'cmd-delete' => mt_rand(10,100),
            'cmd-pause-tube' => mt_rand(10,100),
            'pause-time-left' => mt_rand(10,100),
        ]);

        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("OK %d\r\n%s\r\n", strlen($data), $data));
        $response = $worker->statsTube($name);

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\StatsTubeResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(Yaml::parse($data), $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_OK, $response->getResponse());
        $this->assertSame(sprintf("stats-tube %s\r\n", $name), $connection->getHistory()[0]);
    }

    /**
     * @depends                         testStatsTube
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponseStatsException
     * @expectedExceptionMessageRegExp  /Tube "[^"]+" does not exist/
     */
    public function testStatsTubeNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->statsTube('foo');
    }

    /**
     * @depends                  testStatsTube
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testStatsTubeUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->statsTube('foo');
    }

    public function testStats()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_STATS,
            BeanstalkEvents::POST_DISPATCH_STATS,
        ]);
        $data = Yaml::dump([
            'current-jobs-urgent' => 0,
            'current-jobs-ready' => 0,
            'current-jobs-reserved' => 0,
            'current-jobs-delayed' => 0,
            'current-jobs-buried' => 0,
            'cmd-put' => 0,
            'cmd-peek' => 0,
            'cmd-peek-ready' => 0,
            'cmd-peek-delayed' => 0,
            'cmd-peek-buried' => 0,
            'cmd-reserve' => 0,
            'cmd-use' => 0,
            'cmd-watch' => 0,
            'cmd-ignore' => 0,
            'cmd-delete' => 0,
            'cmd-release' => 0,
            'cmd-bury' => 0,
            'cmd-kick' => 0,
            'cmd-stats' => 0,
            'cmd-stats-job' => 0,
            'cmd-stats-tube' => 0,
            'cmd-list-tubes' => 0,
            'cmd-list-tube-used' => 0,
            'cmd-list-tubes-watched' => 0,
            'cmd-pause-tube' => 0,
            'job-timeouts' => 0,
            'total-jobs' => 0,
            'max-job-size' => 0,
            'current-tubes' => 0,
            'current-connections' => 0,
            'current-producers' => 0,
            'current-workers' => 0,
            'current-waiting' => 0,
            'total-connections' => 0,
            'pid' => 0,
            'version' => 0,
            'rusage-utime' => 0,
            'rusage-stime' => 0,
            'uptime' => 0,
            'binlog-oldest-index' => 'fooBarFoo',
            'binlog-current-index' => 0,
            'binlog-max-size' => 0,
            'binlog-records-written' => 0,
            'binlog-records-migrated' => 0,
            'id' => 0,
            'hostname' => 0,
        ]);

        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("OK %d\r\n%s\r\n", strlen($data), $data));
        $response = $worker->stats();

        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\StatsResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(Yaml::parse($data), $response->getData());
        $this->assertSame('fooBarFoo', $response->getBinlogOldestIndex());

        try {
            $response->getBinlogOldestIndexes();
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException $e) {
            $this->assertRegExp('/Call to undefined method [^:]+::[^\(]+\(\)/', $e->getMessage());
        }

        $this->assertSame(ResponseInterface::RESPONSE_OK, $response->getResponse());
        $this->assertSame("stats\r\n", $connection->getHistory()[0]);
    }

    public function testListTubes()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_LIST_TUBES,
            BeanstalkEvents::POST_DISPATCH_LIST_TUBES,
        ]);
        $tubes = Yaml::dump(['default', 'test_tube1', 'test_tube2', 'test_tube3']);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("OK %d\r\n%s\r\n", strlen($tubes), $tubes));
        $response = $worker->listTubes();
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ListTubesResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(Yaml::parse($tubes), $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_OK, $response->getResponse());
        $this->assertSame("list-tubes\r\n", $connection->getHistory()[0]);
    }

    public function testListTubesUsed()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_LIST_TUBE_USED,
            BeanstalkEvents::POST_DISPATCH_LIST_TUBE_USED,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("USING default\r\n");
        $response = $worker->listTubeUsed();
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ListTubeUsedResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame('default', $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_USING, $response->getResponse());
        $this->assertSame("list-tube-used\r\n", $connection->getHistory()[0]);
    }

    public function testListTubesWatched()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_LIST_TUBES_WATCHED,
            BeanstalkEvents::POST_DISPATCH_LIST_TUBES_WATCHED,
        ]);
        $tubes = Yaml::dump(['default', 'test_tube1']);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse(sprintf("OK %d\r\n%s\r\n", strlen($tubes), $tubes));
        $response = $worker->listTubesWatched();
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\ListTubesWatchedResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(Yaml::parse($tubes), $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_OK, $response->getResponse());
        $this->assertSame("list-tubes-watched\r\n", $connection->getHistory()[0]);
    }

    public function testQuit()
    {
        $dispatcher = $this->getNewMockDispatcherCallable([
            function($name, $event) {
                $this->assertSame($name, BeanstalkEvents::PRE_DISPATCH_QUIT);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PreDispatchEvent', $event);
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Protocol\QuitProtocol', $event->getProtocol());
            },
            function($name, $event){
                $this->assertSame($name, BeanstalkEvents::POST_DISPATCH_QUIT);
                /** @var \PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent $event  */
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Event\PostDispatchEvent', $event);
                $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\QuitResponse', $event->getResponse());
                $this->assertTrue($event->getResponse()->isSuccess());
            },
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $worker->quit();
        $this->assertSame("quit\r\n", $connection->getHistory()[0]);
    }

    public function testPauseTube()
    {
        $dispatcher = $this->getNewMockDispatcherChecKName([
            BeanstalkEvents::PRE_DISPATCH_PAUSE_TUBE,
            BeanstalkEvents::POST_DISPATCH_PAUSE_TUBE,
        ]);
        $worker = $this->getNewBeanstalk($dispatcher)->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("PAUSED\r\n");
        $response = $worker->pauseTube('default', 10);
        $this->assertInstanceOf('PBergman\Bundle\BeanstalkBundle\Response\PauseTubeResponse', $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(null, $response->getData());
        $this->assertSame(ResponseInterface::RESPONSE_PAUSED, $response->getResponse());
        $this->assertSame("pause-tube default 10\r\n", $connection->getHistory()[0]);
    }

    /**
     * @depends                         testPauseTube
     * @expectedException               \PBergman\Bundle\BeanstalkBundle\Exception\ResponsePauseTubeException
     * @expectedExceptionMessageRegExp  /Tube "[^"]+" does not exist/
     */
    public function testPauseTubeNotFound()
    {
        $worker = $this->getNewBeanstalk()->getWorker();
        /** @var ConnectionTestHelper $connection */
        $connection = $worker->getConnection();
        $connection->writeReponse("NOT_FOUND\r\n");
        $worker->pauseTube('default');
    }

    /**
     * @depends                  testPauseTube
     * @expectedException        \PBergman\Bundle\BeanstalkBundle\Exception\ResponseException
     * @expectedExceptionMessage Unknown response: "FOOBAR"
     */
    public function testPauseTubeUnknownResponse()
    {
        $producer = $this->getNewBeanstalk()->getProducer('default');
        /** @var ConnectionTestHelper $connection */
        $connection = $producer->getConnection();
        $connection->writeReponse("FOOBAR\r\n");
        $producer->pauseTube('default');
    }
}