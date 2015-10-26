<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseErrorException extends \Exception implements ExceptionInterface
{
    const OUT_OF_MEMORY = 1;
    const INTERNAL_ERROR = 2;
    const UNKNOWN_COMMAND = 3;
    const BAD_FORMAT = 4;

    static $ERROR_TYPE;

    /**
     * @return  self
     * @throws  self
     */
    static function outOfMemory()
    {
        self::$ERROR_TYPE = self::OUT_OF_MEMORY;
        return new self('The server cannot allocate enough memory for the job. The client should try again later.');
    }

    /**
     * @return  self
     * @throws  self
     */
    static function internalError()
    {
        self::$ERROR_TYPE = self::INTERNAL_ERROR;
        return new self('A bug in the server, please report it at http://groups.google.com/group/beanstalk-talk');
    }

    /**
     * @return  self
     * @throws  self
     */
    static function unknownCommand()
    {
        self::$ERROR_TYPE = self::UNKNOWN_COMMAND;
        return new self('The client sent a command that the server does not know.');
    }

    /**
     * @return  self
     * @throws  self
     */
    static function badFormt()
    {
        self::$ERROR_TYPE = self::BAD_FORMAT;
        return new self('The client sent a command line that was not well-formed.');
    }
}