<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\StatsJobResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StatsProtocol
 *
 * Will give statistical information about the specified job if it exists
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L420-L470
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class StatsJobProtocol extends AbstractProtocol
{
    const COMMAND = 'stats-job';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  StatsJobResponse
     */
    protected function doDispatch(...$payload)
    {
        $data = [];
        if (preg_match(sprintf('/^%s \d+/', StatsJobResponse::RESPONSE_OK), ($response = $this->push(...$payload)))) {
            list($response, $size) = $this->extract($response);
            $data = Yaml::parse($this->read($size));
        }
        return new StatsJobResponse($response, $data);
    }

    /**
     * return the protocol command
     *
     * @param   int $id
     * @return  array|string
     */
    protected function getCommand($id)
    {
        return sprintf('%s %u', $this::COMMAND, $id);
    }
}