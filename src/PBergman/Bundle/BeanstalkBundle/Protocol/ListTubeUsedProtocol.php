<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ListTubeUsedResponse;

/**
 * Class ListTubeUsedProtocol
 *
 * Returns the tube currently being used by the client.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L664-L674
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class ListTubeUsedProtocol extends AbstractProtocol
{
    const COMMAND = 'list-tube-used';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ListTubeUsedResponse
     */
    protected function doDispatch(...$payload)
    {
        return new ListTubeUsedResponse(...$this->extract($this->push(...$payload)));
    }
}