<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Socket;

use PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException;
use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;

/**
 * Class SocketWrapper
 *
 * @package PBergman\Bundle\BeanstalkBundle\Socket
 */
class SocketWrapper
{
    const SELECT_WRITE = 1;
    const SELECT_READ = 2;

    protected $socket;

    /**
     * @param   resource $socket
     * @throws  InvalidArgumentException
     */
    function __construct($socket = null)
    {
        if (!is_null($socket) && !is_resource($socket)) {
            throw new InvalidArgumentException(
                sprintf('Given argument should be a valid resource, given: "%s"', gettype($socket))
            );
        }

        if (!is_null($socket)) {
            $this->socket = $socket;
        }
    }

    /**
     * @inheritdoc
     */
    function __destruct()
    {
        if (is_resource($this->socket)) {
            $this->close();
        }
    }

    /**
     * close socket connection
     *
     * @return bool
     */
    public function close()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }

    /**
     * @param   int  $length
     * @return  null|string
     */
    public function read($length)
    {
        return fread($this->getSocket(), $length);
    }

    /**
     *  read a line from socket
     *
     *  @return string
     */
    public function readLine()
    {
        return fgets($this->getSocket());
    }

    /**
     * @param   mixed   $data
     * @param   null    $length
     * @return  int
     */
    public function write($data, $length = null)
    {
        return fwrite($this->getSocket(), $data, is_null($length) ? strlen($data) : $length);
    }

    /**
     * rewind socket
     *
     * @return bool
     */
    public function rewind()
    {
        return rewind($this->getSocket());
    }

    /**
     * Flushes the output to resource
     *
     * @return bool
     */
    public function flush()
    {
        return fflush($this->getSocket());
    }

    /**
     * @return string
     * @throws ConnectionException
     */
    public function getContent()
    {
        return stream_get_contents($this->getSocket());
    }

    /**
     * @param   int $timeout
     * @return  bool
     */
    public function setTimeOut($timeout)
    {
        return stream_set_timeout($this->getSocket(), $timeout);
    }

    /**
     * @param   bool $isBlocking
     * @return  bool
     */
    public function setBlocking($isBlocking = true)
    {
        return stream_set_blocking($this->getSocket(), (int) $isBlocking);
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return stream_get_meta_data($this->getSocket());
    }

    /**
     * does a select on stream to check if there is something to read or write.
     *
     * @param   int   $mode
     * @param   int   $timeout
     * @return  int
     */
    public function select($mode = self::SELECT_READ, $timeout = 0)
    {
        $args = [[],[],[], $timeout];

        if (self::SELECT_READ  === (self::SELECT_READ  & $mode)) {
            $args[0][] = $this->getSocket();
        }

        if (self::SELECT_WRITE === (self::SELECT_WRITE & $mode)) {
            $args[1][] = $this->getSocket();
        }

        return stream_select(...$args);
    }

    /**
     * @param string    $host
     * @param int       $port
     * @param int       $timeout
     * @param bool      $persistent
     *
     * @return resource
     * @throws ConnectionException
     */
    public function open($host, $port, $timeout, $persistent = true)
    {
        if (!is_null($this->socket)) {
            $this->close();
        }

        if ($persistent) {
            $socket = @pfsockopen($host, $port, $errno, $errstr, $timeout);
        } else {
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        }

        if (false === $socket) {
            throw ConnectionException::CouldNotConnect($host, $port, $errno, $errstr);
        } else {
            return $this->socket = $socket;
        }
    }

    /**
     * @return resource
     * @throws ConnectionException
     */
    public function getSocket()
    {
        if (is_null($this->socket)) {
            throw ConnectionException::NotConnected();
        }

        return $this->socket;
    }
}