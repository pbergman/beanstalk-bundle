<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PeekResponse;

/**
 * Class PeekProtocol
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L360-L387
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PeekProtocol extends AbstractProtocol
{
    const COMMAND = 'peek';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  PeekResponse
     */
    protected function doDispatch(...$payload)
    {
        list($id, $data) = [null, null];
        if (preg_match(sprintf('/^%s/', PeekResponse::RESPONSE_FOUND), ($response = $this->push(...$payload)))) {
            list($response, $id, $size) = $this->extract($response);
            $data = $this->read($size);
        }
        return $this->getNewResponse($response, $data, $id);
    }

    /**
     * @param   string    $response
     * @param   mixed     $data
     * @param   int       $id
     * @return  PeekResponse
     */
    protected function getNewResponse($response, $data, $id)
    {
        return new PeekResponse($response, $data, $id);
    }

    /**
     * return the protocol command
     *
     * @param   int $id
     * @return  string
     */
    protected function getCommand($id)
    {
        return sprintf('%s %u', $this::COMMAND, $id);
    }
}