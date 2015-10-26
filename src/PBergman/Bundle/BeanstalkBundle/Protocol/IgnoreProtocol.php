<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\IgnoreResponse;

/**
 * Class IgnoreProtocol
 *
 * The "ignore" command is for consumers. It removes the named tube from the
 * watch list for the current connection.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L343-L355
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class IgnoreProtocol extends AbstractProtocol
{
    const COMMAND = 'ignore';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  IgnoreResponse
     */
    protected function doDispatch(...$payload)
    {
        return new IgnoreResponse(...$this->extract($this->push(...$payload)));
    }

    /**
     * return the protocol command
     *
     * @param   string $tube
     * @return  string
     */
    protected function getCommand($tube)
    {
        return sprintf('%s %s', self::COMMAND, $tube);
    }


}