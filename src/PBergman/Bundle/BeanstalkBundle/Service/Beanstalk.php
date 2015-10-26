<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Service;

use PBergman\Bundle\BeanstalkBundle\Server\Configuration;
use PBergman\Bundle\BeanstalkBundle\Server\ConfigurationInterface;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;
use PBergman\Bundle\BeanstalkBundle\Server\Manager as ConnectionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Beanstalk
{
    const ACTIVE_WORKERS = 0;
    const ACTIVE_PRODUCERS = 1;
    /** @var ConnectionManager  */
    protected $connectionManager;
    /** @var EventDispatcherInterface */
    protected $dispatcher;
    /** @var array|\SplObjectStorage[]  */
    protected $active = [[],[]];

    /**
     * @param ConnectionManager         $connectionManager
     * @param EventDispatcherInterface  $dispatcher
     */
    function __construct(ConnectionManager $connectionManager, EventDispatcherInterface $dispatcher = null)
    {
        $this->connectionManager = $connectionManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param   string  $cn     connection name, if none given it will use the first one (default)
     *
     * @return  bool|BeanstalkWorker
     * @throws \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     */
    public function getWorker($cn = null)
    {
        $config = $this->connectionManager->getConfiguration($cn);

        if (false === ($worker = $this->findActiveWorker($config))) {
            $worker = new BeanstalkWorker(
                $config,
                $this->dispatcher
            );
            $this->attach($worker, self::ACTIVE_WORKERS);
        }

        return $worker;
    }

    /**
     * @param   string  $cn     connection name, if none given it will use the first one (default)
     *
     * @return  bool|BeanstalkProducer
     * @throws \PBergman\Bundle\BeanstalkBundle\Exception\ConnectionException
     */
    public function getProducer($cn = null)
    {
        $config = $this->connectionManager->getConfiguration($cn);

        if (false === ($producer = $this->findActiveProducer($config))) {
            $producer = new BeanstalkProducer(
                $config,
                $this->dispatcher
            );
            $this->attach($producer, self::ACTIVE_PRODUCERS);
        }

        return $producer;
    }

    /**
     * @param BeanstalkDefaults $beanstalk
     * @param int               $type
     */
    protected function attach(BeanstalkDefaults $beanstalk, $type)
    {
        if (!$this->active[$type] instanceof \SplObjectStorage) {
            $this->active[$type] = new \SplObjectStorage();
        }

        $this->active[$type]->attach($beanstalk, spl_object_hash($beanstalk->getConfig()));
    }

    /**
     * @param   ConfigurationInterface $config
     * @return  bool|BeanstalkWorker
     */
    protected function findActiveWorker(ConfigurationInterface $config)
    {
        return $this->find($config, self::ACTIVE_WORKERS);
    }

    /**
     * @param   ConfigurationInterface $config
     * @return  bool|BeanstalkProducer
     */
    protected function findActiveProducer(ConfigurationInterface $config)
    {
        return $this->find($config, self::ACTIVE_PRODUCERS);
    }

    /**
     * @param   ConfigurationInterface  $config
     * @param   int                     $type
     * @return  bool|object
     */
    protected function find(ConfigurationInterface $config, $type)
    {
        if ($this->active[$type] instanceof \SplObjectStorage && count($this->active[$type]) > 0) {
            $this->active[$type]->rewind();
            while($this->active[$type]->valid()) {
                if (spl_object_hash($config) === $this->active[$type]->getInfo()) {
                    return $this->active[$type]->current();
                }
                $this->active[$type]->next();
            }
        }

        return false;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}