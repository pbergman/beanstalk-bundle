<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\KickJobResponse;

/**
 * Class KickProtocol
 *
 * he kick-job command is a variant of kick that operates
 * with a single job identified by its job id.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L404-L419
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class KickJobProtocol extends AbstractProtocol
{
    const COMMAND = 'kick-job';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  KickJobResponse
     */
    protected function doDispatch(...$payload)
    {
        return new KickJobResponse(...$this->extract($this->push(...$payload)));
    }

    /**
     * return the protocol command
     *
     * @param   int $id
     * @return  string
     */
    protected function getCommand($id)
    {
        return sprintf('%s %u', $this::COMMAND, $id);
    }


}