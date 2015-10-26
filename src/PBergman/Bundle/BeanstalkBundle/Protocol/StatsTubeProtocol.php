<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use PBergman\Bundle\BeanstalkBundle\Response\StatsTubeResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StatsProtocol
 *
 * Will give statistical information about the specified tube if it exists
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L471-L528
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class StatsTubeProtocol extends AbstractProtocol
{
    const COMMAND = 'stats-tube';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    protected function doDispatch(...$payload)
    {
        $data = [];
        if (preg_match(sprintf('/^%s \d+/', ResponseInterface::RESPONSE_OK), ($response = $this->push(...$payload)))) {
            list($response, $size) = $this->extract($response);
            $data = Yaml::parse($this->read($size));
        }
        return new StatsTubeResponse($response, $data);
    }

    /**
     * return the protocol command
     *
     * @param   string $tube
     * @return  string
     */
    protected function getCommand($tube)
    {
        return sprintf('%s %s', $this::COMMAND, $tube);
    }
}