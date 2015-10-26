<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ReserveResponse;

/**
 * Class ReserveProtocol
 *
 * Will return a newly-reserved job
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L196-L254
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class ReserveWithTimeoutProtocol extends ReserveProtocol
{
    const COMMAND = 'reserve-with-timeout';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ReserveResponse
     */
    protected function doDispatch(...$payload)
    {
        $response = $this->extract($this->push(...$payload));
        if (ReserveResponse::RESPONSE_RESERVED === $response[0]) {
            $response[2] = $this->read($response[2]);
        }
        return new ReserveResponse(...$response);
    }

    /**
     * return the protocol command
     *
     * @param   int $timeout
     * @return  string
     */
    protected function getCommand($timeout)
    {
        return sprintf('%s %s', self::COMMAND, $timeout);
    }


}