<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class StreamReadEvent extends Event
{
    /** @var string  */
    protected $read;
    /** @var string  */
    protected $time;

    /**
     * @param string $read      the string that was return by server from reading
     * @param string $time      a unix timestamp with microtime
     *
     */
    function __construct($read, $time)
    {
        $this->read = $read;
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

}