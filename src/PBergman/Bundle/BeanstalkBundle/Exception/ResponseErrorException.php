<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseErrorException extends ResponseException
{
    /**
     * @return  self
     * @throws  self
     */
    static function outOfMemory()
    {
        return new self('The server cannot allocate enough memory for the job. The client should try again later.', self::OUT_OF_MEMORY);
    }

    /**
     * @return  self
     * @throws  self
     */
    static function internalError()
    {
        return new self('A bug in the server, please report it at http://groups.google.com/group/beanstalk-talk', self::INTERNAL_ERROR);
    }

    /**
     * @return  self
     * @throws  self
     */
    static function unknownCommand()
    {
        return new self('The client sent a command that the server does not know.', self::UNKNOWN_COMMAND);
    }

    /**
     * @return  self
     * @throws  self
     */
    static function badFormt()
    {
        return new self('The client sent a command line that was not well-formed.', self::BAD_FORMAT);
    }
}