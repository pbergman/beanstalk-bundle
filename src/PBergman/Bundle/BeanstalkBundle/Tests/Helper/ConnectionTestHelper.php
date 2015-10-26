<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\Bundle\BeanstalkBundle\Tests\Helper;

use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Socket\SocketWrapper;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConnectionTestHelper
 *
 * @codeCoverageIgnore
 * @package PBergman\Bundle\BeanstalkBundle\Tests\Helper
 */
class ConnectionTestHelper extends SocketWrapper implements ConnectionInterface
{
    /** @var array  */
    protected $history = [null];

    public function __construct(Configuration $config = null)
    {
        parent::__construct(fopen('php://memory', 'w+'));
    }

    /**
     * @param   mixed $data
     * @param   null $length
     * @return  int
     */
    public function write($data, $length = null)
    {
        $index = count($this->history) > 0 ? count($this->history) - 1 : 0;
        $this->history[$index] .= $data;
    }

    /**
     * Flushes the output to resource
     *
     * @return bool
     */
    public function flush()
    {
        $this->history[count($this->history)] = null;
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        return array_filter($this->history);
    }

    /**
     * @param $data
     */
    public function writeReponse($data)
    {
        parent::write($data);
        parent::flush();
        parent::rewind();
    }

    public function reset()
    {
        $this->history = [null];
        parent::__construct(fopen('php://memory', 'w+'));
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * @return bool
     */
    public function hasDispatcher()
    {
        return false;
    }
}