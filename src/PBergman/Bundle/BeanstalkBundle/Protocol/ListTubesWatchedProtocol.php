<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ListTubesWatchedResponse;

/**
 * Class ListTubesWatchedProtocol
 *
 * Returns a list tubes currently being watched bythe client
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L675-L689
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 * @method  ListTubesWatchedResponse doDispatch(...$payload)
 */
class ListTubesWatchedProtocol extends ListTubesProtocol
{
    const COMMAND = 'list-tubes-watched';

    /**
     * @param  string $response
     * @param  string $data
     * @return ListTubesWatchedResponse
     */
    protected function getNewResponse($response, $data)
    {
        return new ListTubesWatchedResponse($response, $data);
    }
}