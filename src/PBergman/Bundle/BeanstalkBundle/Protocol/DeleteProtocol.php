<?php
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\DeleteResponse;

/**
 * Class DeleteProtocol
 *
 * The delete command removes a job from the server entirely. It is normally used
 * by the client when the job has successfully run to completion. A client can
 * delete jobs that it has reserved, ready jobs, delayed jobs, and jobs that are
 * buried.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L251-L267
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class DeleteProtocol extends AbstractProtocol
{
    const COMMAND = 'delete';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  DeleteResponse
     */
    protected function doDispatch(...$payload)
    {
        return new DeleteResponse($this->push(...$payload), $payload[0]);
    }

    /**
     * @inheritdoc
     */
    protected function getCommand($id)
    {
        return sprintf('%s %u', $this::COMMAND, $id);
    }
}