<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Exception\InvalidArgumentException;
use PBergman\Bundle\BeanstalkBundle\Exception\ResponseErrorException;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;
use PBergman\Bundle\BeanstalkBundle\Server\ConnectionInterface;

abstract class AbstractProtocol implements ProtocolInterface
{
    /** @var ConnectionInterface  */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * dispatch command
     *
     * @param   $payload
     * @return  ResponseInterface
     * @throws  InvalidArgumentException
     */
    public function dispatch(...$payload)
    {
        if (!($ret = $this->doDispatch(...$payload)) instanceof ResponseInterface) {
            throw new InvalidArgumentException (
                'The doDispatch method should return a response that implements ResponseInterface'
            );
        }

        return $ret;
    }

    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    abstract protected function doDispatch(...$payload);

    /**
     * return the protocol command
     *
     * @param   $payload
     * @return  array|string
     */
    protected function getCommand(...$payload)
    {
        return $this::COMMAND;
    }


    /**
     * will push command and return first line of response
     *
     * @param   $payload
     * @return  array
     * @throws  ResponseErrorException
     */
    protected function push(...$payload)
    {
        foreach ((array) $this->getCommand(...$payload) as $command) {
            $this->connection->write($command);
            $this->connection->write(self::CRLF);
            $this->connection->flush();
        }

        return $this->validateReturn(
            trim($this->connection->readLine())
        );
    }

    /**
     * will extract string by splitting it with space
     *
     * @param   string  $data
     * @return  array
     */
    protected function extract($data)
    {
        return explode(' ', $data);
    }

    /**
     * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L33-L60
     *
     * @param   string
     * @return  string
     * @throws  ResponseErrorException
     */
    protected function validateReturn($data)
    {
        switch ($data) {
            case 'OUT_OF_MEMORY':
                throw ResponseErrorException::outOfMemory();
                break;
            case 'INTERNAL_ERROR':
                throw ResponseErrorException::internalError();
                break;
            case 'UNKNOWN_COMMAND':
                throw ResponseErrorException::unknownCommand();
                break;
            case 'BAD_FORMAT':
                throw ResponseErrorException::badFormt();
                break;
            default:
                return $data;
        }
    }

    /**
     * read/trim from steam given bytes
     *
     * @param   $size
     * @return  string
     */
    protected function read($size)
    {
        return trim($this->connection->read($size));
    }
}