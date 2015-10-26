<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Server;

use PBergman\Bundle\BeanstalkBundle\BeanstalkEvents;
use PBergman\Bundle\BeanstalkBundle\Event\StreamReadEvent;
use PBergman\Bundle\BeanstalkBundle\Event\StreamWriteEvent;
use PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException;
use PBergman\Bundle\BeanstalkBundle\Socket\SocketWrapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Connection extends SocketWrapper implements ConnectionInterface
{
    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    /**
     * @param   Configuration $config
     * @throws  ConnectionException
     */
    function __construct(Configuration $config = null)
    {
        if ($config instanceof Configuration) {
            return parent::open(
                $config->getHost(),
                $config->getPort(),
                $config->getTimeout(),
                $config->isPersistent()
            );
        }
        parent::__construct();
    }

    /**
     * @param   $socket
     * @return  $this
     */
    public function setSocket($socket)
    {
        parent::__construct($socket);
        return $this;
    }

    /**
     * return timestamp, a unix timestamp with dot and micrtime as suffix
     *
     * @return string
     */
    protected function getTimeStamp()
    {
        $utime = microtime(true);
        return (new \DateTime(date(sprintf('Y-m-d H:i:s.%06d', ($utime - floor($utime)) * 1000000), $utime)))->format('U.u');
    }

    /**
     * @inheritdoc
     */
    public function write($data, $length = null)
    {
        if (!is_null($this->dispatcher) && $this->dispatcher->hasListeners(BeanstalkEvents::STREAM_WRITE)) {
            $this->dispatcher->dispatch(
                BeanstalkEvents::STREAM_WRITE,
                new StreamWriteEvent(
                    is_null($length) ? $data : substr($data, 0, $length),
                    $this->getTimeStamp()
                )
            );
        }
        return parent::write($data, $length);
    }

    /**
     * @@inheritdoc
     */
    public function read($length)
    {
        $read = parent::read($length);
        if (!is_null($this->dispatcher) && $this->dispatcher->hasListeners(BeanstalkEvents::STREAM_READ)) {
            $this->dispatcher->dispatch(
                BeanstalkEvents::STREAM_READ,
                new StreamReadEvent($read, $this->getTimeStamp())
            );
        }
        return $read;
    }

    /**
     * @@inheritdoc
     */
    public function readLine()
    {
        $read = parent::readLine();
        if (!is_null($this->dispatcher) && $this->dispatcher->hasListeners(BeanstalkEvents::STREAM_READ)) {
            $this->dispatcher->dispatch(
                BeanstalkEvents::STREAM_READ,
                new StreamReadEvent($read, $this->getTimeStamp())
            );
        }
        return $read;
    }


    /**
     * @param   EventDispatcherInterface $dispatcher
     * @return  $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasDispatcher()
    {
        return !is_null($this->dispatcher);
    }
}