<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\TouchResponse;

/**
 * Class TouchProtocol
 *
 * Subsequent put commands will put jobs into the tube specified by this command.
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L308-L326
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class TouchProtocol extends AbstractProtocol
{
    const COMMAND = 'touch';

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  TouchResponse
     */
    protected function doDispatch(...$payload)
    {
        return new TouchResponse($this->push(...$payload));
    }

    /**
     * @inheritdoc
     */
    protected function getCommand($id)
    {
        return sprintf('%s %u', $this::COMMAND, $id);
    }


}