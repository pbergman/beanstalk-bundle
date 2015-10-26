<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Service;

use PBergman\Bundle\BeanstalkBundle\Protocol;
use PBergman\Bundle\BeanstalkBundle\Response;
use PBergman\Bundle\BeanstalkBundle\Exception;
use PBergman\Bundle\BeanstalkBundle\Event;
use PBergman\Bundle\BeanstalkBundle\Server\ConfigurationInterface;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BeanstalkDefaults
 *
 * @package PBergman\Bundle\BeanstalkBundle\Service
 */
class BeanstalkDefaults
{
    const MAX_PRIORITY = (2**32-1); // Max 32 bits
    /** @var ConfigurationInterface  */
    protected $config;
    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    /**
     * @param ConfigurationInterface    $config
     * @param EventDispatcherInterface  $dispatcher
     */
    function __construct(ConfigurationInterface $config, EventDispatcherInterface $dispatcher = null)
    {
        $this->config = $config;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        if (!is_null($this->dispatcher) && !$this->config->getConnection()->hasDispatcher()) {
            $this->config->getConnection()->setDispatcher($this->dispatcher);
        }

        return $this->config->getConnection();
    }

    /**
     * @param   Protocol\AbstractProtocol $protocol
     * @param   $payload
     * @return  null|Response\ResponseInterface
     */
    protected function dispatch(Protocol\AbstractProtocol $protocol, ...$payload)
    {
        $this->preDispatch($protocol, $payload);
        $response = $protocol->dispatch(...$payload);
        $this->postDispatch($response, $protocol::COMMAND);
        return $response;
    }

    /**
     * @param Protocol\AbstractProtocol $protocol
     * @param $payload
     */
    public function preDispatch(Protocol\AbstractProtocol $protocol, &$payload)
    {
        $eventName = sprintf('beanstalk.pre.dispatch.%s', str_replace('-', '.', $protocol::COMMAND));
        if (!is_null($this->dispatcher) && $this->dispatcher->hasListeners($eventName)) {
            $this->dispatcher->dispatch(
                $eventName,
                new Event\PreDispatchEvent($protocol, $payload)
            );
        }
    }

    /**
     * @param Response\ResponseInterface   $response
     * @param string                       $command
     */
    public function postDispatch(Response\ResponseInterface $response, $command)
    {
        $eventName = sprintf('beanstalk.post.dispatch.%s', str_replace('-', '.', $command));
        if (!is_null($this->dispatcher) && $this->dispatcher->hasListeners($eventName)) {
            $this->dispatcher->dispatch(
                $eventName,
                new Event\PostDispatchEvent($response)
            );
        }
    }

    /**
     * small helper to validate all peek protocols
     *
     * @param   Response\ResponseInterface $response
     * @return  Response\ResponseInterface
     * @throws  Exception\ResponsePeekException
     */
    protected function peekValidate(Response\ResponseInterface $response)
    {
        switch ($response->getResponse()) {
            case $response::RESPONSE_FOUND:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponsePeekException::notFound();
                break;
            default:
                throw Exception\ResponsePeekException::unknownResponse($response->getResponse());
        }
    }

    /**
     * return ($id) job
     *
     * @param   int $id
     * @return  Response\PeekResponse
     * @throws  Exception\ResponsePeekException
     */
    public function peek($id)
    {
        return $this->peekValidate($this->dispatch(
            new Protocol\PeekProtocol($this->getConnection()), $id
        ));
    }


    /**
     * Return the next ready job.
     *
     * @return  Response\PeekReadyResponse
     * @throws  Exception\ResponsePeekException
     */
    public function peekReady()
    {
        return $this->peekValidate($this->dispatch(
            new Protocol\PeekReadyProtocol($this->getConnection())
        ));
    }

    /**
     *  Return the delayed job with the shortest delay left.
     *
     * @return  Response\PeekDelayedResponse
     * @throws  Exception\ResponsePeekException
     */
    public function peekDelayed()
    {
        return $this->peekValidate($this->dispatch(
            new Protocol\PeekDelayedProtocol($this->getConnection())
        ));
    }

    /**
     * Return the next job in the list of buried jobs.
     *
     * @return  Response\PeekBuriedResponse
     * @throws  Exception\ResponsePeekException
     */
    public function peekBuried()
    {
        return $this->peekValidate($this->dispatch(
            new Protocol\PeekBuriedProtocol($this->getConnection())
        ));
    }

    /**
     * It moves ($bound) jobs into the ready queue. If there
     * are any buried jobs, it will only kick buried jobs.
     *
     * @param   $bound
     * @return  Response\KickResponse
     */
    public function kick($bound)
    {
        return $this->dispatch(
            new Protocol\KickProtocol($this->getConnection()), $bound
        );
    }

    /**
     * If the given job id exists and is in a buried or delayed state,
     * it will be moved to the ready queue of the the same tube where it
     * currently belongs
     *
     * @param   int     $id
     * @return  Response\KickJobResponse
     * @throws  Exception\ResponseKickJobException
     */
    public function kickJob($id)
    {
        $response = $this->dispatch(
            new Protocol\KickJobProtocol($this->getConnection()), $id
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_KICKED:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseKickJobException::jobDoesNotExistOrNotKickable($id);
                break;
            default:
                throw Exception\ResponseKickJobException::unknownResponse($response->getResponse());
        }
    }

    /**
     * Get statistical information about the specified job if it exists
     *
     * @param   int $id
     * @return Response\StatsJobResponse
     * @throws Exception\ResponseStatsException
     */
    public function statsJob($id)
    {
        $response = $this->dispatch(
            new Protocol\StatsJobProtocol($this->getConnection()), $id
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_OK:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseStatsException::jobDoesNotExist($id);
                break;
            default:
                throw Exception\ResponseStatsException::unknownResponse($response->getResponse());
                break;
        }
    }

    /**
     * Get statistical information about the specified tube if it exists
     *
     * @param   string  $tube
     * @return  Response\StatsTubeResponse
     * @throws  Exception\ResponseStatsException
     */
    public function statsTube($tube)
    {
        $response = $this->dispatch(
            new Protocol\StatsTubeProtocol($this->getConnection()), $tube
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_OK:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseStatsException::tubeDoesNotExist($tube);
                break;
            default:
                throw Exception\ResponseStatsException::unknownResponse($response->getResponse());
                break;
        }
    }

    /**
     * Get statistical information about the system.
     *
     * @return Response\StatsResponse
     */
    public function stats()
    {
        return $this->dispatch(
            new Protocol\StatsProtocol($this->getConnection())
        );
    }
//
    /**
     * Returns a list of all existing tubes.
     *
     * @return Response\ListTubesResponse
     */
    public function listTubes()
    {
        return $this->dispatch(
            new Protocol\ListTubesProtocol($this->getConnection())
        );
    }

    /**
     *  Returns the tube currently being used by the client.
     *
     * @return Response\ListTubeUsedResponse
     */
    public function listTubeUsed()
    {
        return $this->dispatch(
            new Protocol\ListTubeUsedProtocol($this->getConnection())
        );
    }

    /**
     * Returns a list tubes currently being watched by the client.
     *
     * @return Response\ListTubesWatchedResponse
     */
    public function listTubesWatched()
    {
        return $this->dispatch(
            new Protocol\ListTubesWatchedProtocol($this->getConnection())
        );
    }

    /**
     * Will simply closes the connection.
     *
     * @return void
     */
    public function quit()
    {
        $this->dispatch(
            new Protocol\QuitProtocol($this->getConnection())
        );
    }

    /**
     * The pause-tube command can delay any new job being reserved for a given time.
     *
     * @param   string  $tube
     * @param   int     $delay
     * @return  Response\PauseTubeResponse
     * @throws  Exception\ResponsePauseTubeException
     */
    public function pauseTube($tube, $delay = 0)
    {
        $response = $this->dispatch(
            new Protocol\PauseTubeProtocol($this->getConnection()), $tube, $delay
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_PAUSED:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponsePauseTubeException::tubeDoesNotExist($tube);
                break;
            default:
                throw Exception\ResponsePauseTubeException::unknownResponse($response->getResponse());
        }
    }
}