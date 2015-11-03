<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

use Exception;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

class ResponseException extends \Exception implements ExceptionInterface
{
    const BURIED          = 1;
    const EXPECTED_CRLF   = 2;
    const JOB_TOO_BIG     = 4;
    const DRAINING        = 8;
    const DEADLINE_SOON   = 16;
    const TIMED_OUT       = 32;
    const RESERVED        = 64;
    const NOT_FOUND       = 128;
    const NOT_IGNORED     = 256;
    const UNKNOWN         = 512;
    const OUT_OF_MEMORY   = 1024;
    const INTERNAL_ERROR  = 2048;
    const UNKNOWN_COMMAND = 4096;
    const BAD_FORMAT      = 8192;

    /**
     * @param   string    $response
     * @return  $this
     */
    public static function unknownResponse($response)
    {
        return new self(sprintf('Unknown response: "%s"', $response), self::UNKNOWN);
    }
}