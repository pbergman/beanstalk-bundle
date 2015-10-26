<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

use Exception;
use PBergman\Bundle\BeanstalkBundle\Response\ResponseInterface;

class ResponseBuryException extends ResponseException
{
    use Traits\JobTrait;
}