<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ListTubesResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ListTubesProtocol
 *
 * Returns a list of all existing tubes
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L650-L665
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class ListTubesProtocol extends AbstractProtocol
{
    const COMMAND = 'list-tubes';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ListTubesResponse
     */
    protected function doDispatch(...$payload)
    {
        list ($response, $size) = $this->extract($this->push());
        return $this->getNewResponse($response, Yaml::parse($this->read($size)));
    }

    /**
     * @param string $response
     * @param string $data
     * @return ListTubesResponse
     */
    protected function getNewResponse($response, $data)
    {
        return new ListTubesResponse($response, $data);
    }
}