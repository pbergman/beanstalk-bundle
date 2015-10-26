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
    /**
     * @param   string    $response
     * @return  $this
     */
    public static function unknownResponse($response)
    {
        return new self(sprintf('Unknown response: "%s"', $response));
    }
}