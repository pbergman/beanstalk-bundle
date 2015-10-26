<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\KickResponse;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

/**
 * Class KickProtocol
 *
 * It moves jobs into the ready queue If there are any buried jobs, it
 * will only kick buried jobs. Otherwise it will kick delayed jobs
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L389-L402
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class KickProtocol extends AbstractProtocol
{
    const COMMAND = 'kick';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    protected function doDispatch(...$payload)
    {
        return new KickResponse(...$this->extract($this->push(...$payload)));

    }

    /**
     * return the protocol command
     *
     * @param   int $bound
     * @return  string
     */
    protected function getCommand($bound)
    {
        return sprintf('%s %u', $this::COMMAND, $bound);
    }
}