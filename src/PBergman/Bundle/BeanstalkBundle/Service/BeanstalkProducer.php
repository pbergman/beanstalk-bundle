<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Service;

use PBergman\Bundle\BeanstalkBundle\Exception\ResponseException;
use PBergman\Bundle\BeanstalkBundle\Exception\ResponsePutException;
use PBergman\Bundle\BeanstalkBundle\Protocol\PutProtocol;
use PBergman\Bundle\BeanstalkBundle\Protocol\UseProtocol;
use PBergman\Bundle\BeanstalkBundle\Response\PutResponse;

class BeanstalkProducer extends BeanstalkDefaults
{
    protected $usingTube = 'default';

    /**
     * Insert a job into the queue.
     *
     * @param   mixed $data
     * @param   int $priority
     * @param   int $delay
     * @param   int $ttr
     * @return  null|PutResponse
     * @throws  ResponsePutException|ResponseException
     */
    public function put($data, $priority = self::MAX_PRIORITY , $delay = 0, $ttr = 60)
    {
        $response = $this->dispatch(
            new PutProtocol($this->getConnection()),
            $data, $priority, $delay, $ttr
        );

        switch ($response->getResponse()) {
            case PutResponse::RESPONSE_EXPECTED_CRLF:
                throw ResponsePutException::expectedCRLF();
                break;
            case PutResponse::RESPONSE_JOB_TOO_BIG:
                throw ResponsePutException::jobToBig();
                break;
            case PutResponse::RESPONSE_DRAINING:
                throw ResponsePutException::draining();
                break;
            case PutResponse::RESPONSE_BURIED:
                throw ResponsePutException::buried($response);
                break;
            case PutResponse::RESPONSE_INSERTED:
                return $response;
                break;
            default:
                throw ResponsePutException::unknownResponse($response->getResponse());
        }
    }

    /**
     *  Subsequent put commands will put jobs into the tube specified by this command.
     *
     * @param   string    $tube
     * @return  $this
     */
    public function useTube($tube)
    {
        $this->usingTube = $this->dispatch(new UseProtocol($this->getConnection()), $tube)->getData();
        return $this;
    }

    /**
     * @return string
     */
    public function getUsingTube()
    {
        return $this->usingTube;
    }
}