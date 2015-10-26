<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class StreamWriteEvent extends Event
{
    /** @var string  */
    protected $write;
    /** @var string  */
    protected $time;

    /**
     * @param string $write     the string that is been writen to the stream
     * @param string $time      a unix timestamp with microtime
     *
     */
    function __construct($write, $time)
    {
        $this->write = $write;
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getWrite()
    {
        return $this->write;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

}