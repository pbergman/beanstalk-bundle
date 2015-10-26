<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PeekReadyResponse;

/**
 * Class PeekReadyProtocol
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L360-L387
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PeekReadyProtocol extends PeekProtocol
{
    const COMMAND = 'peek-ready';

    protected function getNewResponse($response, $data, $id)
    {
        return new PeekReadyResponse($response, $data, $id);
    }

    /**
     * return the protocol command
     *
     * @param   $payload
     * @return  array|string
     */
    protected function getCommand(...$payload)
    {
        return self::COMMAND;
    }
}