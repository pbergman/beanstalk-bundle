<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ConnectionException extends \Exception implements ExceptionInterface
{
    /**
     * @param   string     $name
     * @return  self
     */
    static function NoConnectionDefinedByName($name)
    {
        return new self(sprintf('No connection defined by name: "%s"', $name));
    }

    /**
     * @return  self
     */
    static function NoConnectionDefined()
    {
        return new self('No connections defined!');
    }

    /**
     * @codeCoverageIgnore
     * @return  self
     */
    static function NotConnected()
    {
        return new self('There is no active connection.');
    }

    /**
     * @param   int     $code
     * @param   string  $message
     * @return  self
     */
    static function CouldNotConnect($host, $port, $code, $message)
    {
        return new self(sprintf('Unable to connect %s:%s, %s(%s)', $host, $port, $message, $code));
    }

}