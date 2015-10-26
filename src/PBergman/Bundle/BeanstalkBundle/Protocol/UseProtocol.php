<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\UseResponse;

/**
 * Class UseProtocol
 *
 * Subsequent put commands will put jobs into the tube specified by this command.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L175-L188
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class UseProtocol extends AbstractProtocol
{
    const COMMAND = 'use';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  UseResponse
     */
    protected function doDispatch(...$payload)
    {
        return new UseResponse(...$this->extract($this->push(...$payload)));
    }

    /**
     * return the protocol command
     *
     * @param   string  $tube
     * @return  string
     */
    protected function getCommand($tube)
    {
        return sprintf('%s %s', $this::COMMAND, $tube);
    }
}