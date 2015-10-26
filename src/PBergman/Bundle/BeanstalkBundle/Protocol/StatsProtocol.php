<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\StatsResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StatsProtocol
 *
 * The stats command gives statistical information about the system as a whole
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L527-L648
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class StatsProtocol extends AbstractProtocol
{
    const COMMAND = 'stats';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  StatsResponse
     */
    protected function doDispatch(...$payload)
    {
        list($response, $size) = $this->extract($this->push(...$payload));
        return new StatsResponse($response, Yaml::parse($this->read($size)));
    }
}