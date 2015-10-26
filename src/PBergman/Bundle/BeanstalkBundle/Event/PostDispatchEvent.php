<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Event;

use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PostDispatchEvent extends Event
{
    /** @var ResponseInterface */
    protected $response;

    /**
     * @param ResponseInterface $response
     */
    function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}