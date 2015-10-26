<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PutResponse;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

/**
 * Class PutProtocol
 *
 * The "put" command is for any process that wants to insert a job into the queue.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L123-L173
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PutProtocol extends AbstractProtocol
{
    const COMMAND = 'put';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    protected function doDispatch(...$payload)
    {
        return new PutResponse(...$this->extract($this->push(...$payload)));
    }

    /**
     * @param   int     $priority
     * @param   int     $delay
     * @param   int     $ttr
     * @param   string  $data
     * @return  array
     */
    protected function getCommand($data, $priority, $delay, $ttr)
    {
        return [
            sprintf('%s %u %u %u %u', $this::COMMAND, $priority, $delay, $ttr, strlen($data)),
            sprintf('%s', $data),
        ];
    }
}
