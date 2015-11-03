<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponseDeleteException extends ResponseException
{
    /**
     * @return $this
     */
    static function notFound()
    {
        return new self(
            "The job does not exist or is not either reserved by the\nclient, ready, or buried. This could happen if the job\ntimed out before the client sent the delete command.",
            self::NOT_FOUND
        );
    }
}