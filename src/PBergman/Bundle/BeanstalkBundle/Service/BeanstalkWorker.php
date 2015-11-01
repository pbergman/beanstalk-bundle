<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Service;

use PBergman\Bundle\BeanstalkBundle\Exception;
use PBergman\Bundle\BeanstalkBundle\Protocol;
use PBergman\Bundle\BeanstalkBundle\Response;

class BeanstalkWorker extends BeanstalkDefaults
{
    /** @var array  */
    protected $watchedTubes = ['default'];

    /**
     * This will return a newly-reserved job. If no job is available to be reserved,
     * beanstalkd will wait to send a response until one becomes available. Once a
     * job is reserved for the client, the client has limited time to run (TTR) the
     * job before the job times out. When the job times out, the server will put the
     * job back into the ready queue. Both the TTR and the actual time left can be
     * found in response to the stats-job command.
     *
     *
     * @param   int|null   $timeout
     *
     * @return  Response\ReserveResponse
     * @throws  Exception\ResponseReserveException
     */
    public function reserve($timeout = null)
    {
        if (!is_null($timeout)) {
            $response = $this->dispatch(
                new Protocol\ReserveWithTimeoutProtocol($this->getConnection()), $timeout
            );
        } else {
            $response = $this->dispatch(
                new Protocol\ReserveProtocol($this->getConnection())
            );
        }

        Exception\ResponseReserveException::$RESPONSE = $response->getResponse();

        switch ($response->getResponse()) {
            case $response::RESPONSE_DEADLINE_SOON:
                throw Exception\ResponseReserveException::deadlineSoon();
                break;
            case $response::RESPONSE_TIMED_OUT:
                throw Exception\ResponseReserveException::timeout();
                break;
            case $response::RESPONSE_RESERVED:
                return $response;
                break;
            default:
                throw Exception\ResponseReserveException::unknownResponse($response->getResponse());
        }
    }

    /**
     * A client can delete jobs that it has reserved, ready
     * jobs, delayed jobs, and jobs that are buried.
     *
     * @param   int $id
     * @return  Response\DeleteResponse
     * @throws  Exception\ResponseDeleteException
     */
    public function delete($id)
    {
        $response = $this->dispatch(
            new Protocol\DeleteProtocol($this->getConnection()), $id
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_DELETED:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw new Exception\ResponseDeleteException(
                    'The job does not exist or is not either reserved by the
                    client, ready, or buried. This could happen if the job
                    timed out before the client sent the delete command.'
                );
                break;
            default:
                throw Exception\ResponseDeleteException::unknownResponse($response->getResponse());
        }
    }

    /**
     * Put a reserved job back into the ready queue.
     *
     * @param int   $id
     * @param int   $priority
     * @param int   $delay
     *
     * @return Response\ReleaseResponse
     * @throws Exception\ResponseReleaseException
     */
    public function release($id, $priority = self::MAX_PRIORITY, $delay = 0)
    {
        $response = $this->dispatch(
            new Protocol\ReleaseProtocol($this->getConnection()),
            $id, $priority, $delay
        );

        switch ($response->getResponse()) {
            case $response::RESPONSE_BURIED:
                throw Exception\ResponseReleaseException::buried($response);
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseReleaseException::jobDoesNotExistOrNotReservedByClient($id);
                break;
            case $response::RESPONSE_RELEASED:
                return $response;
                break;
            default:
                throw Exception\ResponseReleaseException::unknownResponse($response->getResponse());
        }

    }

    /**
     * Buried jobs are put into a FIFO linked list and will
     * not be touched by the server again until a client
     * kicks them with the "kick" command.
     *
     * @param   int   $id
     * @param   int   $priority
     *
     * @return  Response\BuryResponse
     * @throws  Exception\ResponseBuryException
     */
    public function bury($id, $priority = self::MAX_PRIORITY)
    {
        $response = $this->dispatch(
            new Protocol\BuryProtocol($this->getConnection()),
            $id, $priority
        );
        switch ($response->getResponse()) {
            case $response::RESPONSE_BURIED:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseBuryException::jobDoesNotExistOrNotReservedByClient($id);
                break;
            default:
                throw Exception\ResponseBuryException::unknownResponse($response->getResponse());
        }
    }

    /**
     * This allows a worker to request more time to work on a job.
     *
     * @param   int $id
     *
     * @return  Response\TouchResponse
     * @throws  Exception\ResponseTouchException
     */
    public function touch($id)
    {
        $response = $this->dispatch(
            new Protocol\TouchProtocol($this->getConnection()), $id
        );
        switch($response->getResponse()) {
            case $response::RESPONSE_TOUCHED:
                return $response;
                break;
            case $response::RESPONSE_NOT_FOUND:
                throw Exception\ResponseTouchException::jobDoesNotExistOrNotReservedByClient($id);
                break;
            default:
                throw Exception\ResponseTouchException::unknownResponse($response->getResponse());
        }
    }

    /**
     * @param   string  $tube
     *
     * @return  Response\WatchResponse
     */
    public function watch($tube)
    {
        $this->isValidTubeName($tube);

        return $this->dispatch(
            new Protocol\WatchProtocol($this->getConnection()), $tube
        );
    }

    /**
     * @param   string    $tube
     *
     * @return  Response\IgnoreResponse
     * @throws  Exception\ResponseIgnoreException
     */
    public function ignore($tube)
    {
        $response = $this->dispatch(
            new Protocol\IgnoreProtocol($this->getConnection()), $tube
        );
        switch($response->getResponse()) {
            case $response::RESPONSE_NOT_IGNORED:
                throw new Exception\ResponseIgnoreException('Trying to ignore the only tube in its watch list.');
                break;
            case $response::RESPONSE_WATCHING:
                $this->remWatchedTube($tube);
                break;
            default:
                throw Exception\ResponseIgnoreException::unknownResponse($response->getResponse());
        }

        return $response;
    }

    /**
     * add tube to watched list
     *
     * @param string $tube
     * @codeCoverageIgnore
     */
    protected function addWatchedTube($tube)
    {
        if (false === array_search($tube, $this->watchedTubes)) {
            $this->watchedTubes[] = $tube;
        }
    }

    /**
     * remove tube from watch list
     *
     * @param string $tube
     * @codeCoverageIgnore
     */
    protected function remWatchedTube($tube)
    {
        if (false !== ($index = array_search($tube, $this->watchedTubes))) {
            unset($this->watchedTubes[$index]);
        }
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getWatchedTubes()
    {
        return $this->watchedTubes;
    }
}