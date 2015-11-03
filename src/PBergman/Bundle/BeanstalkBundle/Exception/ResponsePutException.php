<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponsePutException extends ResponseException
{
    use Traits\BuriedTrait;

    /**
     * @return ResponsePutException
     */
    static function expectedCRLF()
    {
        return new self(
            "The job body must be followed by a CR-LF pair, that is, \"\\r\\n\".\nThese two bytes are not counted in the job size given by the client\nin the put command line.",
            self::EXPECTED_CRLF
        );
    }

    /**
     * @return ResponsePutException
     */
    static function jobToBig()
    {
        return new self(
            'The client has requested to put a job with a body larger than max-job-size bytes',
            self::JOB_TOO_BIG
        );
    }

    /**
     * @return ResponsePutException
     */
    static function draining()
    {
        return new self(
            "The server has been put into \"drain mode\" and is no longer accepting new\njobs. The client should try another server or disconnect and try again later.",
            self::DRAINING
        );
    }
}