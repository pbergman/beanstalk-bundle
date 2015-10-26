<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use PBergman\Bundle\BeanstalkBundle\Protocol\AbstractProtocol;
use Symfony\Component\EventDispatcher\Event;

class PreDispatchEvent extends Event
{
    /** @var AbstractProtocol */
    protected $protocol;
    /** @var mixed */
    protected $payload;

    /**
     * @param AbstractProtocol $protocol
     * @param mixed            $payload;
     */
    function __construct(AbstractProtocol $protocol, &$payload)
    {
        $this->protocol = $protocol;
        $this->payload = &$payload;
    }

    /**
     * @return AbstractProtocol
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}