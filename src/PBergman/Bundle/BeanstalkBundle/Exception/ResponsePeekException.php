<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponsePeekException extends ResponseException
{
    /**
     * @return  self
     * @throws  self
     */
    static function notFound()
    {
        return new self('The requested job doesn\'t exist or there are no jobs in the requested state');
    }
}