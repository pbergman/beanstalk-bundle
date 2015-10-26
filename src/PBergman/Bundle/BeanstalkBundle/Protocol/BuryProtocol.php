<?php
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\BuryResponse;

/**
 * Class BuryProtocol
 *
 * The delete command removes a job from the server entirely. It is normally used
 * by the client when the job has successfully run to completion. A client can
 * delete jobs that it has reserved, ready jobs, delayed jobs, and jobs that are
 * buried.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L290-L307
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class BuryProtocol extends AbstractProtocol
{
    const COMMAND = 'bury';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  BuryResponse
     */
    protected function doDispatch(...$payload)
    {
        return new BuryResponse($this->push(...$payload));
    }

    /**
     * @inheritdoc
     */
    protected function getCommand($id, $priority)
    {
        return sprintf('%s %s %s', $this::COMMAND, $id, $priority);
    }
}