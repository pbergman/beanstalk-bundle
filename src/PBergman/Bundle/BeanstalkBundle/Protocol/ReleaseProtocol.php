<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ReleaseResponse;

/**
 * Class ReleaseProtocol
 *
 * The release command puts a reserved job back into the ready
 * queue (and marks its state as "ready") to be run by any client
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L268-L289
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class ReleaseProtocol extends AbstractProtocol
{
    const COMMAND = 'release';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ReleaseResponse
     */
    protected function doDispatch(...$payload)
    {
        return new ReleaseResponse($this->push(...$payload), $payload[0]);
    }

    /**
     * @inheritdoc
     */
    protected function getCommand($id, $priority, $delay)
    {
        return sprintf('%s %s %s %s', $this::COMMAND, $id, $priority, $delay);
    }

}