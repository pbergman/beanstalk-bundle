<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Server;

use PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ConnectionInterface
{
    /**
     * @param   Configuration $config
     * @throws  ConnectionException
     */
    public function __construct(Configuration $config = null);

    /**
     * close stream connection
     *
     * @return bool
     */
    public function close();

    /**
     * @param   int  $length
     * @return  null|string
     */
    public function read($length);

    /**
     *  read a line from socket
     */
    public function readLine();

    /**
     * @param   mixed   $data
     * @param   null    $length
     * @return  int
     */
    public function write($data, $length = null);

    /**
     * Flushes the output to resource
     *
     * @return bool
     */
    public function flush();

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher);

    /**
     * @return bool
     */
    public function hasDispatcher();

    /**
     * @param string    $host
     * @param int       $port
     * @param int       $timeout
     * @param bool      $persistent
     *
     * @return resource
     * @throws ConnectionException
     */
    public function open($host, $port, $timeout, $persistent = true);

    /**
     * @return resource
     * @throws ConnectionException
     */
    public function getSocket();
}
