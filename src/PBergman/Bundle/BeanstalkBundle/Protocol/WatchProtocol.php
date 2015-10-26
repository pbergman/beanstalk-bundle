<?php
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\WatchResponse;

/**
 * Class WatchProtocol
 *
 * The "watch" command adds the named tube to the watch list for the current
 * connection. A reserve command will take a job from any of the tubes in the
 * watch list. For each new connection, the watch list initially consists of one
 * tube, named "default".
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L327-L342
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class WatchProtocol extends AbstractProtocol
{
    const COMMAND = 'watch';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  WatchResponse
     */
    protected function doDispatch(...$payload)
    {
        return new WatchResponse(...$this->extract($this->push(...$payload)));
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