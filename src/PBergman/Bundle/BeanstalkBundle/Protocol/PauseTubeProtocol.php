<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PauseTubeResponse;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

/**
 * Class PauseTubeProtocol
 *
 * Delay any new job being reserved for a given time
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L696-L708
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PauseTubeProtocol extends AbstractProtocol
{
    const COMMAND = 'pause-tube';

    /**
     * dispatching command to server
     *
     * @param   ...$payload
     * @return  ResponseInterface
     */
    protected function doDispatch(...$payload)
    {
        return new PauseTubeResponse(...$this->extract($this->push(...$payload)));
    }

    /**
     * return the protocol command
     *
     * @param   string    $tube
     * @param   int       $delay
     * @return  string
     */
    protected function getCommand($tube, $delay)
    {
        return sprintf('%s %s %u', self::COMMAND, $tube, $delay);
    }
}