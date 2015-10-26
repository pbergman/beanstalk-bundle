<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Bundle\BeanstalkBundle\Exception;

class ResponsePauseTubeException extends ResponseException
{
    use Traits\TubeTrait;
}