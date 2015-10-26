<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PeekDelayedResponse;

/**
 * Class PeekDelayedProtocol
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L360-L387
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PeekDelayedProtocol extends PeekProtocol
{
    const COMMAND = 'peek-delayed';

    /**
     * @inheritdoc
     */
    protected function getNewResponse($response, $data, $id)
    {
        return new PeekDelayedResponse($response, $data, $id);
    }

    /**
     * @inheritdoc
     */
    protected function getCommand(...$payload)
    {
        return self::COMMAND;
    }
}