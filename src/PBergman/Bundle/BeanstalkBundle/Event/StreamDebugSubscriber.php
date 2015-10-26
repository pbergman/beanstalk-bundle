<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;
use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StreamDebugSubscriber
 *
 * a helper debug class to trace all i/o with stream
 *
 * @package PBergman\Bundle\BeanstalkBundle\Event
 */
class StreamDebugSubscriber implements EventSubscriberInterface
{
    const TRACE_READ = 1;
    const TRACE_WRITE = 2;

    /** @var array  */
    protected $traceMaxSize = array(
        self::TRACE_READ  => 10,
        self::TRACE_WRITE => 10
    );

    /** @var array  */
    protected $traces = array(
        self::TRACE_READ  => [],
        self::TRACE_WRITE => [],
    );


    /**
     * @return  array
     */
    public function getTraceMaxSize()
    {
        return $this->traceMaxSize;
    }

    /**
     * @param       int   $max
     * @param       int   $type
     * @return      $this
     */
    public function setTraceMaxSize($max, $type = self::TRACE_READ | self::TRACE_WRITE)
    {

        if (self::TRACE_READ === (self::TRACE_READ & $type)) {
            $this->traceMaxSize[self::TRACE_READ] = $max;
        }

        if (self::TRACE_WRITE === (self::TRACE_WRITE & $type)) {
            $this->traceMaxSize[self::TRACE_WRITE] = $max;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTraces()
    {
        return $this->traces;
    }

    /**
     * @return array
     */
    public function getReadTraces()
    {
        return $this->traces[self::TRACE_READ];
    }

    /**
     * @return array
     */
    public function getWriteTraces()
    {
        return $this->traces[self::TRACE_WRITE];
    }

    /**
     * push to trace
     *
     * @param string $line
     * @param int $type
     */
    protected function pushTrace($line, $stamp, $type)
    {
        if (count($this->traces[$type]) >=  $this->traceMaxSize[$type]) {
            array_shift($this->traces[$type]);
        }
        $this->traces[$type][$stamp] = $line;
        ksort($this->traces[$type]);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BeanstalkEvents::STREAM_READ =>  'pushResponse',
            BeanstalkEvents::STREAM_WRITE => 'pushResponse',
        ];
    }

    /**
     * @param Event $event
     */
    public function pushResponse(Event $event)
    {

        if($event instanceof StreamReadEvent) {
            $this->pushTrace($event->getRead(), $event->getTime(), self::TRACE_READ);
            return;
        }

        if($event instanceof StreamWriteEvent) {
            $this->pushTrace($event->getWrite(), $event->getTime(), self::TRACE_WRITE);
            return;
        }

        throw new InvalidArgumentException(sprintf('Invalid Event: "%s"', get_class($event)));
    }

}