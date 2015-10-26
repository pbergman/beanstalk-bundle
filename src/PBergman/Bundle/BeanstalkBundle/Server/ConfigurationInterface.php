<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Server;

interface ConfigurationInterface
{
    /**
     * @return string
     */
    public function getHost();

    /**
     * @return int
     */
    public function getPort();

    /**
     * @return int
     */
    public function getTimeout();

    /**
     * @return boolean
     */
    public function isPersistent();

    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * closses connection and setup new connection
     *
     * @return  Connection;
     */
    public function reconnect();

    /**
     * @param  ConnectionInterface $connection
     * @return $this;
     */
    public function setConnection(ConnectionInterface $connection);
}