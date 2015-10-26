<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;

interface ProtocolInterface
{
    const CRLF = "\r\n";
    // The base command
    const COMMAND = null;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection);

    /**
     * dispatch command
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    public function dispatch(...$payload);

}