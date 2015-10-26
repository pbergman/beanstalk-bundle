<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Protocol;

use PBergman\Bundle\BeanstalkBundle\Response\QuitResponse;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

/**
 * Class QuitProtocol
 *
 * The quit command simply closes the connection
 *
 * @see https://github.com/kr/beanstalkd/blob/master/doc/protocol.txt#L690-L693
 *
 * @package PBergman\Bundle\BeanstalkBundle\Protocol
 */
class QuitProtocol extends AbstractProtocol
{
    const COMMAND = 'quit';

    /**
     * dispatch command
     *
     * @param   $payload
     * @return  QuitResponse
     */
    public function dispatch(...$payload)
    {
        return new QuitResponse(...$this->doDispatch(...$payload));
    }


    /**
     * dispatching command to server
     *
     * @param   $payload
     * @return  ResponseInterface
     */
    protected function doDispatch(...$payload)
    {
        $this->connection->write($this->getCommand());
        $this->connection->write(self::CRLF);
        $this->connection->flush();
        $this->connection->close();
        return [];
    }
}