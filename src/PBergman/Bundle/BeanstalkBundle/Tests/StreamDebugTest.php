<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests;

use PBergman\Bundle\BeanstalkBundle\Event\StreamDebugSubscriber;
use PBergman\Bundle\BeanstalkBundle\Server\Connection;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StreamDebugTest extends \PHPUnit_Framework_TestCase
{
    public function testSetters()
    {
        $debugger = new StreamDebugSubscriber();
        $debugger->setTraceMaxSize(5, $debugger::TRACE_READ);
        $this->assertSame(10, $debugger->getTraceMaxSize()[$debugger::TRACE_WRITE]);
        $this->assertSame(5,  $debugger->getTraceMaxSize()[$debugger::TRACE_READ]);
        $debugger->setTraceMaxSize(5, $debugger::TRACE_WRITE);
        $this->assertSame(5,  $debugger->getTraceMaxSize()[$debugger::TRACE_WRITE]);
        $this->assertSame(5,  $debugger->getTraceMaxSize()[$debugger::TRACE_READ]);
        $debugger->setTraceMaxSize(2);
        $this->assertSame(2,  $debugger->getTraceMaxSize()[$debugger::TRACE_WRITE]);
        $this->assertSame(2,  $debugger->getTraceMaxSize()[$debugger::TRACE_READ]);
    }

    public function testInstance()
    {
        $debugger = new StreamDebugSubscriber();
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($debugger);
        $connection = (new Connection())
            ->setSocket(fopen('php://memory', 'r+'))
            ->setDispatcher($dispatcher)
        ;
        $debugger->setTraceMaxSize(2);
        for ($i = 0; $i < 10; $i++) {
            $connection->write($i);
        }
        $this->assertSame([8,9], array_values($debugger->getWriteTraces()));
        $connection->rewind();
        $this->assertSame('01234', $connection->read(5));
        $this->assertSame('56789', $connection->readLine());
        $this->assertSame(['01234','56789'], array_values($debugger->getReadTraces()));
        $this->assertSame(['01234','56789'], array_values($debugger->getTraces()[$debugger::TRACE_READ]));
        try {
            $debugger->pushResponse(new Event());
        } catch (\PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException $e) {
            $this->assertRegExp('/Invalid Event: "[^"]+"/', $e->getMessage());
        }
    }

}