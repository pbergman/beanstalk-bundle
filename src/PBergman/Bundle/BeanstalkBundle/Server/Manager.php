<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Server;

use PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException;
use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;

class Manager implements \Iterator, \ArrayAccess, \Countable
{
    /** @var \SplObjectStorage  */
    protected $connections;

    /**
     * @inheritdoc
     */
    function __construct()
    {
        $this->connections = new \SplObjectStorage();
    }

    /**
     * @param   string                 $name
     * @param   ConfigurationInterface $conn
     * @return  $this;
     */
    public function addConfiguration($name, ConfigurationInterface $conn)
    {
        $this->connections->attach($conn, $name);
        return $this;
    }

    /**
     * @param   string $name
     * @throws  ConnectionException
     */
    public function removeConnection($name)
    {
        if (false !== ($obj = $this->findByName($name))) {
            $this->connections->detach($obj);
        } else {
            throw ConnectionException::NoConnectionDefinedByName($name);
        }
    }

    /**
     * @param   string  $name
     * @return  bool|ConfigurationInterface
     * @throws  ConnectionException
     */
    public function getConfiguration($name = null)
    {
        if ($this->connections->count() === 0) {
            throw ConnectionException::NoConnectionDefined();
        }

        if (!$name) {
            return $this->getFirst();
        } else {
            if (false === ($conn = $this->findByName($name))) {
                throw ConnectionException::NoConnectionDefinedByName($name);
            } else {
                return $conn;
            }
        }
    }

    /**
     * @return ConfigurationInterface
     */
    protected function getFirst()
    {
        $this->connections->rewind();
        return $this->connections->current();
    }

    /**
     * @param   string    $name
     * @return  bool|ConfigurationInterface
     */
    protected function findByName($name)
    {
        $this->connections->rewind();
        while($this->connections->valid()) {
            if($this->connections->getInfo() === $name) {
                return $this->connections->current();
            }
            $this->connections->next();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->connections->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->connections->next();
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->connections->key();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->connections->valid();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->connections->rewind();
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return false !== $this->findByName($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->getConfiguration($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->addConfiguration(is_null($offset) ? count($this->connections) : $offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->removeConnection($offset);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->connections->count();
    }
}