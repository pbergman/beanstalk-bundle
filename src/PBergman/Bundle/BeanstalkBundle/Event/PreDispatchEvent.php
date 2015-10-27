<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PreDispatchEvent extends Event
{
    /** @var mixed */
    protected $payload;
    /** @var string  */
    protected $command;

    /**
     * @param mixed     $payload ;
     * @param string    $command
     */
    function __construct(&$payload, $command)
    {
        $this->payload = &$payload;
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}