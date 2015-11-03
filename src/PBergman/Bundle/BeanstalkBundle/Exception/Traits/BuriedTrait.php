<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception\Traits;

use PBergman\Bundle\BeanstalkBundle\Exception\ResponseException;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

trait BuriedTrait
{
    /** @var ResponseInterface  */
    protected $response;

    /**
     * @param   ResponseInterface $response
     * @return  $this
     */
    static function buried(ResponseInterface $response)
    {
        return (new self(
            'The server ran out of memory trying to grow the priority queue data structure.', ResponseException::BURIED
        ))->setResponse($response);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param  ResponseInterface $response
     * @return $this;
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }
}