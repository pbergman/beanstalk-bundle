<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseReserveException extends ResponseException
{
    const CODE_DEADLINE_SOON = 128;
    const CODE_TIMEOUT       = 64;

    /**
     * @return ResponseReserveException
     */
    static function deadlineSoon()
    {
        return new self(
            'The client issues a reserve command during the safety margin, or the safety margin arrives while the client is waiting on a reserve command.',
            self::CODE_DEADLINE_SOON
        );
    }

    /**
     * @return ResponseReserveException
     */
    static function timeout()
    {
        return new self(
            'A non-negative timeout was specified and the timeout exceeded before a job became available, or if the client\'s connection is half-closed,',
            self::CODE_TIMEOUT
        );
    }
}