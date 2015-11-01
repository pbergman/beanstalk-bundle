<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

class ResponseReserveException extends ResponseException
{
    /** @var string  */
    protected $response;

    /**
     * @return ResponseReserveException
     */
    static function deadlineSoon()
    {
        return (new self(
            'The client issues a reserve command during the safety margin, or the safety margin arrives while the client is waiting on a reserve command.'
        ))->setResponse(ResponseInterface::RESPONSE_DEADLINE_SOON);
    }

    /**
     * @return ResponseReserveException
     */
    static function timeout()
    {
        return (new self(
            'A non-negative timeout was specified and the timeout exceeded before a job became available, or if the client\'s connection is half-closed,'
        ))->setResponse(ResponseInterface::RESPONSE_TIMED_OUT);
    }

    /**
     * @param   string  $response
     * @return  $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}