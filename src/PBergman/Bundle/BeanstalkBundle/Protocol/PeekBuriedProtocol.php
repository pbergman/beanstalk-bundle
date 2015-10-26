<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\PeekBuriedResponse;

/**
 * Class PeekBuriedProtocol
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L360-L387
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class PeekBuriedProtocol extends PeekProtocol
{
    const COMMAND = 'peek-buried';

    /**
     * @inheritdoc
     */
    protected function getNewResponse($response, $data, $id)
    {
        return new PeekBuriedResponse($response, $data, $id);
    }


    /**
     * @inheritdoc
     */
    protected function getCommand(...$payload)
    {
        return $this::COMMAND;
    }
}