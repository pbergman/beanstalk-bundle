<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Server;

class Configuration implements ConfigurationInterface
{
    /** @var string  */
    protected $host;
    /** @var int */
    protected $port;
    /** @var int  */
    protected $timeout;
    /** @var bool */
    protected $persistent;
    /** @var ConnectionInterface  */
    protected $connection;

    /**
     * @param string    $host
     * @param int       $port
     * @param int       $timeout
     * @param bool      $persistent
     */
    function __construct($host, $port = 11300, $timeout = null, $persistent = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->persistent = $persistent;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return boolean
     */
    public function isPersistent()
    {
        return $this->persistent;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        if (is_null($this->connection)) {
            return $this->connection = new Connection($this);
        }

        return $this->connection;
    }

    /**
     * closses connection and setup new connection
     *
     * @return ConnectionInterface
     */
    public function reconnect()
    {
        $conn = $this->getConnection();
        $conn->close();
        return $this->connection = new $conn($this);
    }

    /**
     * @param  ConnectionInterface $connection
     * @return $this;
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        return $this;
    }
}